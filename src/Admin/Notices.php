<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Admin\Utils\Notice;
use Irmmr\WpNotifBell\Db;

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
                    __('All notifications you selected have been successfully removed.', 'notif-bell'),
                    Notice::SUCCESS,
                    true
                ];
                break;
                
            case 'list-bulk-error':
                $data = [
                    __('Your request could not be processed.', 'notif-bell'),
                    Notice::ERROR,
                    true
                ];
                break;

            case 'list-bulk-uns':
                $data = [
                    __('Please select at least 1 notif to take actions.', 'notif-bell'),
                    Notice::WARN,
                    true
                ];
                break;

            case 'list-bulk-nonce':
                $data = [
                    __('The security nonce is invalid! Please refresh the page.', 'notif-bell'),
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
     * add all auto notices
     * 
     * @since   0.9.0
     * @return  void
     */
    protected function add_auto_notices(): void
    {
        // check plugin database version
        if (!Db::is_last_version()) {
            $message = __('The plugin database is outdated and needs to be updated. Please update the database now by going to the tools section.', 'notif-bell');
            $message .= sprintf('<a class="button wpnb-btn-adm" href="%s">%s</a>', menu_page_url('wpnb-tools', false), __('Update', 'notif-bell'));

            Notice::print($message, Notice::ERROR, false);
        }
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
            [$msg, $level, $dismiss] = $this->get_msg_notice( sanitize_key($_GET['wpnb-msg']) );

            if ('' !== $msg) {
                Notice::print($msg, $level, $dismiss);
            }
        }

        // auto notices
        $this->add_auto_notices();
    }
}