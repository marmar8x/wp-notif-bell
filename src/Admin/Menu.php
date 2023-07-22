<?php

namespace Irmmr\WpNotifBell\Admin;

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
            __('About', 'wp-notif-bell'),
            __('About', 'wp-notif-bell'),
            'manage_options',
            'wpnb-about',
            [$this, 'about_content'],
            'dashicons-bell'
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
     * [content -> about] get about menu content
     * 
     * @since    0.9.0
     * @return   void
     */
    public function about_content(): void
    {
        Partial::req('about-page', ['admin']);
    }
}