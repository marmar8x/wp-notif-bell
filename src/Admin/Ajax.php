<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Container;
use Irmmr\WpNotifBell\Helpers\Data;
use Irmmr\WpNotifBell\Helpers\Notif;
use Irmmr\WpNotifBell\Logger;
use Irmmr\WpNotifBell\Notif\Collector;
use Irmmr\WpNotifBell\Notif\Instance\Receiver;
use Irmmr\WpNotifBell\Notif\Sender;
use Irmmr\WpNotifBell\Notif\Updater;
use Irmmr\WpNotifBell\Traits\DateTrait;

/**
 * Class Ajax
 * admin side ajax request handler
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
class Ajax
{
    use DateTrait;

    /**
     * class constructor
     * 
     * @since    0.9.0
     */
    public function __construct()
    {
        add_action('wp_ajax_wpnb_send_notif', [$this, 'res_send_notif']);
        add_action('wp_ajax_wpnb_edit_notif', [$this, 'res_edit_notif']);
    }

    /**
     * get error message
     * 
     * @since    0.9.0
     * @param    string  $code
     * @return   string
     */
    private function get_error_msg(string $code): string
    {
        if (substr($code, 0, 10) === 'incomplete') {
            return __('The data sent is incomplete.', 'notif-bell');
        } elseif ($code === 'invalid-receivers') {
            return __('Recipients are either not entered or not entered correctly.', 'notif-bell');
        } elseif ($code === 'invalid-tags') {
            return __('The tags are not entered correctly.', 'notif-bell');
        } elseif ($code === 'invalid-date') {
            return __('The sending date entered is invalid.', 'notif-bell');
        } elseif ($code === 'invalid-format') {
            return __('The entered format is not defined.', 'notif-bell');
        }

        return __('Unknown error', 'notif-bell');
    }

    /**
     * check notif data and validate
     * 
     * @since    0.9.0
     * @param    array  $request
     * @param    mixed  &$notif
     * @return   string
     */
    private function check_notif_data(array $request, &$notif): string
    {
        // notif data keys
        $notif_keys = [
            'title',
            'sender',
            'text',
            'format',
            'receivers',
            'date'
        ];

        // required fields
        $notif_keys_req = [
            'title',
            'sender',
            'text',
            'format'
        ];

        // unslash data
        $uns_keys = [
            'title',
            'text',
        ];

        // check notif data
        if (!isset($request['notif']) || !is_array($request['notif'])) {
            return 'incomplete-ba';
        }

        // get main notif data
        $notif = $request['notif'];

        // check every notif key
        foreach ($notif_keys as $key) {
            if (!isset($notif[$key])) {
                return 'incomplete';
            }
        }

        // check every notif required key
        foreach ($notif_keys_req as $key) {
            if (empty($notif[$key])) {
                return 'incomplete-fill';
            }
        }

        // unslash data
        foreach ($uns_keys as $key) {
            $notif[$key] = wp_unslash($notif[$key]);
        }

        if (!is_array($notif['receivers']) || count($notif['receivers']) === 0) {
            return 'invalid-receivers';
        }

        if (isset($notif['tags']) && !empty($notif['tags']) && !is_array($notif['tags'])) {
            return 'invalid-tags';
        }

        if (!empty($notif['date']) && !Data::is_datetime($notif['date'])) {
            return 'invalid-date';
        }

        if (!Notif::is_valid_format($notif['format'])) {
            return 'invalid-format';
        }

        // re-fetch receivers list
        $notif['receivers'] = array_filter($notif['receivers'], function ($r) {
            return isset($r['name']) && isset($r['data']);
        });

        // sanitize the whole data
        foreach ([ 'title', 'sender', 'format', 'date' ] as $key) {
            if (isset($notif[ $key ])) {
                $notif[ $key ] = sanitize_text_field($notif[ $key ]);
            }
        }

        return '';
    }

    /**
     * ajax function
     * send notification for send-page
     * 
     * @since    0.9.0
     * @return  void
     */
    public function res_send_notif(): void
    {
        // check security nonce
        check_ajax_referer('wpnb_nnc_send', 'security');

        // request entry
        $request = $_POST;

        // get all data errors
        $error = $this->check_notif_data($request, $notif);

        // check notif
        if ('' !== $error) {
            wp_send_json_error([
                'error' => $error,
                'msg'   => $this->get_error_msg($error)
            ]);

            exit;
        }

        // create a notif sender
        $nb_sender = new Sender;

        // [Debug] runs only when debugging
        if (Container::$debugging) {
            Logger::add('Ajax (admin): notif data received via ajax - send', Logger::N_DEBUG, Logger::LEVEL_LOG, (array) $notif);
        }

        // pass values
        $nb_sender
            ->set_title($notif['title'])
            ->set_content($notif['text'])
            ->set_format($notif['format'])
            ->set_sender($notif['sender']);

        // set sent_at
        if (!empty($notif['date'])) {
            $nb_sender->set_date($notif['date']);
        }

        // add tags
        if (isset($notif['tags']) && !empty($notif['tags'])) {
            $nb_sender->set_tags($notif['tags']);
        }
        
        // add every receiver
        foreach ($notif['receivers'] as $receiver) {
            $nb_sender->add_receiver(new Receiver(
                wp_unslash($receiver['name']),
                wp_unslash($receiver['data'])
            ));
        }

        // send notif
        $nb_sender->send();

        // show the sender result
        wp_send_json_success($nb_sender->get_result_esc());

        exit;
    }


    /**
     * ajax function
     * edit notification for send-page (update)
     * 
     * @since    0.9.0
     * @return  void
     */
    public function res_edit_notif(): void
    {
        // check security nonce (same as send)
        check_ajax_referer('wpnb_nnc_send', 'security');

        // request entry
        $request = $_POST;

        // get all data errors
        $error = $this->check_notif_data($request, $notif);

        // check notif
        if ('' !== $error) {
            wp_send_json_error([
                'error' => $error,
                'msg'   => $this->get_error_msg($error)
            ]);

            exit;
        }

        // check if notif exists for updating (std)
        if (!empty($request['key'] ?? '')) {
            if (!(new Collector)->target_by_key($request['key'])->has()) {
                wp_send_json_error([
                    'error' => 'not-found',
                    'msg'   => __('No Notif were found with the entered key.', 'notif-bell')
                ]);
    
                exit;
            }
        }

        // create a notif sender
        $nb_updater = new Updater;

        // [Debug] runs only when debugging
        if (Container::$debugging) {
            Logger::add('Ajax (admin): notif data received via ajax - update', Logger::N_DEBUG, Logger::LEVEL_LOG, (array) $notif);
        }

        // pass values
        $nb_updater
            ->set_title($notif['title'])
            ->set_content($notif['text'])
            ->set_format($notif['format'])
            ->set_sender($notif['sender']);

        // set sent_at
        $nb_updater->set_date(!empty($notif['date']) ? $notif['date'] : $this->get_current_date());

        // add tags
        if (!empty($notif['tags'])) {
            $nb_updater->set_tags($notif['tags']);
        }
        
        // add every receiver
        $receivers = [];

        foreach ($notif['receivers'] as $receiver) {
            $receivers[] = new Receiver(
                wp_unslash($receiver['name']),
                wp_unslash($receiver['data'])
            );
        }

        $nb_updater->set_receivers($receivers);

        // update notif
        $nb_updater->find_by_key($request['key'])->run();

        // show the sender result
        wp_send_json_success($nb_updater->get_result_esc());

        exit;
    }
}