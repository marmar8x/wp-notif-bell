<?php

namespace Irmmr\WpNotifBell\Admin;

/**
 * Class Assets
 * register assets file
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
class Assets
{
    /**
     * class constructor
     * 
     * @since    0.9.0
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'register']);
    }

    /**
     * register assets files
     * 
     * @since    0.9.0
     * @return   void
     */
    public function register(): void
    {
        if (is_rtl()) {
            $this->styles_rtl();
        } else {
            $this->styles_ltr();
        }
    }

    /**
     * ltr styles files
     * 
     * @since    0.9.0
     * @return   void
     */
    public function styles_ltr(): void
    {
        wp_enqueue_style('wpnb_core_ltr', IRM_WP_NOTIF_BELL_URL . 'assets/dist/css/admin.css?refID=9158021', false, IRM_WP_NOTIF_BELL_VER);
    }

    /**
     * rtl styles files
     * 
     * @since    0.9.0
     * @return   void
     */
    public function styles_rtl(): void
    {
        wp_enqueue_style('wpnb_core_rtl', IRM_WP_NOTIF_BELL_URL . 'assets/dist/css/admin.rtl.css?refID=98021', false, IRM_WP_NOTIF_BELL_VER);
    }
}