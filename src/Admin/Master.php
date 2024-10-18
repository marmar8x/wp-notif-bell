<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Master
 * start anything about admin panel
 * and require all other admin classes
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
final class Master
{
    /**
     * load admin files from "include"
     * 
     * @since    0.9.0
     * @return   void
     */
    public function includes(): void
    {
        // include/admin/settings.php
        wpnb_safe_require(MM8X_WP_NOTIF_BELL_PTH, 'include', 'admin', 'settings.php');

        // include/admin/send.php
        wpnb_safe_require(MM8X_WP_NOTIF_BELL_PTH, 'include', 'admin', 'send.php');
    }

    /**
     * init admin
     * 
     * @since    0.9.0
     * @return   void
     */
    public function init(): void
    {
        // easy access list
        $eza_list = Statics::$eza_list = [
            'all-command' => [
                'data'  => 'command:all',
                'value' => __('Send to all users', 'notif-bell')
            ],
            'role-subs'   => [
                'data'  => 'user-role:subscriber',
                'value' => __('Send to Subscribers', 'notif-bell')
            ]
        ];

        /**
         * !! without check
         * filter of easy access list (menu)
         * 
         * @since   0.9.0
         * @param   array   $eza_list   List of all eza
         */
        $eza_list = (array) apply_filters('wpnb_adm_eza_list', $eza_list);

        // update the list
        Statics::$eza_list = $eza_list;

        // receiver names list
        $rec_list = Statics::$rec_list = [];

        /**
         * !! without check
         * filter of rec names list
         * 
         * @since   0.9.0
         * @param   array   $rec_list   List of all receivers name
         */
        $rec_list = (array) apply_filters('wpnb_adm_rec_list', $rec_list);

        // update the list
        Statics::$rec_list = $rec_list;
    }

    /**
     * class constructor
     * admin-side processes
     * 
     * @since    0.9.0
     */
    public function __construct()
    {
        // init statics ...
        add_action('admin_init', [$this, 'init']);

        // includes req files with admin init
        add_action('admin_init', [$this, 'includes']);

        new Assets;
        new Menu;
        new Settings;
        new Notices;
        new Ajax;
    }
}