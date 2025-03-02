<?php

namespace Irmmr\WpNotifBell\Core;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Api\Starter;
use Irmmr\WpNotifBell\Helpers\Data;
use Irmmr\WpNotifBell\Notif\Collector;
use Irmmr\WpNotifBell\Settings;
use Irmmr\WpNotifBell\User;

/**
 * Class Api
 * create ajax routes for wp site
 * this type of request are only using for
 * own site simple requests in admin or website.
 *
 * !! Note: for more security you can just **DISABLE** "ajax"
 * and use wp-rest in /api
 *
 * # wpnb_add_seen    ->      notif_id     [single]
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Core
 */
class Api
{
    // @since   0.9.0
    // protected const NAMESPACE = 'wpnb/v1';

    /**
     * Api construct
     * 
     * @since   0.9.0
     */
    public function __construct()
    {
        // check api ajax status
        if (Settings::get('api.ajax.status') === 'enable') {
            // add ajax: add_seen
            if (Settings::get('api.ajax.add_seen_list') === 'enable') {
                add_action('wp_ajax_wpnb_add_seen', [$this, 'ajax_add_seen']);
            }
        }

        // rest api
        if (Settings::get('api.rest.status', 'enable') === 'enable') {
            new Starter();
        }
    }

    /**
     * ajax: add notif id to seen list
     * nonce => token:wpnb_ajax_add_seen
     * 
     * @since   0.9.0
     * @return  void
     */
    public function ajax_add_seen(): void
    {
        if (isset($_POST['notif_id']) && isset($_POST['token'])) {
            $body    = $_POST;
        } else {
            $input    = file_get_contents('php://input');

            // check input of POST req
            if (empty($input) || !Data::is_json($input)) {
                wp_send_json_error([ 'code' => 400 ], 400);

                exit;
            }

            $body = json_decode($input, true);
        }

        // check security nonce
        if (!isset($body['token']) || !wp_verify_nonce( sanitize_key($body['token']), 'wpnb_ajax_add_seen' )) {
            wp_send_json_error([ 'code' => 403 ], 403);

            exit;
        }

        // sanitize data
        $notif_id = intval($body['notif_id'] ?? '');

        // check for notif_id data type and current user
        if (empty($notif_id) || get_current_user_id() === 0) {
            wp_send_json_error([ 'code' => 400 ]);

            exit;
        }

        $collector = new Collector;
        $collector->select()->where()->equals('id', $notif_id)->end();

        // check if notif_id is valid
        if (!$collector->has()) {
            wp_send_json_success([ 'result' => false ]);

            exit;
        }

        // update user seen list
        $result = User::add_seen_list($notif_id, get_current_user_id());

        wp_send_json_success([ 'result' => $result ]);

        exit;
    }
}