<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Partial;

/**
 * Class Menu
 * manage and insert admin page menu
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
class Menu
{
    /**
     * class constructor
     * 
     * @since    0.9.0
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register']);
    }

    /**
     * [main] register all menus in wp
     * 
     * @since    0.9.0
     * @return   void
     */
    public function register(): void
    {
        add_menu_page(
            __('Notif Bell', 'wp-notif-bell'),
            __('Notif Bell', 'wp-notif-bell'),
            'manage_options',
            'wpnb-main',
            [$this, 'main_content'],
            'dashicons-bell'
        );

        add_submenu_page(
            'wpnb-main',
            __('Send', 'wp-notif-bell'),
            __('Send', 'wp-notif-bell'),
            'wpnb_can_send',
            'wpnb-send',
            [$this, 'send_content']
        );

        add_submenu_page(
            'wpnb-main',
            __('List', 'wp-notif-bell'),
            __('List', 'wp-notif-bell'),
            'manage_options',
            'wpnb-list',
            [$this, 'list_content']
        );

        add_submenu_page(
            'wpnb-main',
            __('Settings', 'wp-notif-bell'),
            __('Settings', 'wp-notif-bell'),
            'manage_options',
            'wpnb-settings',
            [$this, 'settings_content']
        );
    }

    /**
     * [content -> main] get main menu content
     * 
     * @since    0.9.0
     * @return   void
     */
    public function main_content(): void
    {
        Partial::req('main-page', ['admin']);
    }

   /**
     * [content -> settings] get settings menu content
     * 
     * @since    0.9.0
     * @return   void
     */
    public function settings_content(): void
    {
        Partial::req('settings-page', ['admin']);
    }

   /**
     * [content -> send] get send menu content
     * 
     * @since    0.9.0
     * @return   void
     */
    public function send_content(): void
    {
        Partial::req('send-page', ['admin']);
    }

   /**
     * [content -> list] get list menu content
     * 
     * @since    0.9.0
     * @return   void
     */
    public function list_content(): void
    {
        Partial::req('list-page', ['admin']);
    }
}