<?php

namespace Irmmr\WpNotifBell\Admin;

use Irmmr\WpNotifBell\Admin\Utils\Notice;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Notices
 * show admin notices in wp-admin
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
class Notices
{
    /**
     * get message notices with key
     * 
     * @since   0.9.0
     * @param   string  $name
     * @return  array
     */
    protected function get_msg_notice(string $name): array
    {
        switch ($name) {
            case 'list-bulk-removed':
                $data = [
                    __('All notifications you selected have been successfully removed.', 'wp-notif-bell'),
                    Notice::SUCCESS,
                    true
                ];
                break;
                
            case 'list-bulk-error':
                $data = [
                    __('Your request could not be processed.', 'wp-notif-bell'),
                    Notice::ERROR,
                    true
                ];
                break;

            case 'list-bulk-uns':
                $data = [
                    __('Please select at least 1 notif to take actions.', 'wp-notif-bell'),
                    Notice::WARN,
                    true
                ];
                break;

            case 'list-bulk-nonce':
                $data = [
                    __('The security nonce is invalid! Please refresh the page.', 'wp-notif-bell'),
                    Notice::ERROR,
                    true
                ];
                break;

            default:
                $data = ['', '', true];
                break;
        }

        return $data;
    }

    /**
     * class constructor
     * 
     * @since    0.9.0
     */
    public function __construct()
    {
        add_action('admin_notices', [$this, 'init']);
    }

    /**
     * add all notices base on $_GET[wpnb-msg]
     * 
     * @since   0.9.0
     * @return  void
     */
    public function init(): void
    {
        // showing notices using 'wpnb-msg' get method
        if (isset($_GET['wpnb-msg'])) {
            [$msg, $level, $dismiss] = $this->get_msg_notice($_GET['wpnb-msg']);

            if ('' !== $msg) {
                Notice::print($msg, $level, $dismiss);
            }
        }

        // auto notices
    }
}