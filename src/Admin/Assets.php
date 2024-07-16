<?php

namespace Irmmr\WpNotifBell\Admin;

use Irmmr\WpNotifBell\I18n;

// If this file is called directly, abort.
defined('WPINC') || die;

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
        $this->styles_lib();

        if (is_rtl()) {
            $this->styles_rtl();
        } else {
            $this->styles_ltr();
        }

        $this->scripts();
    }

    /**
     * lib styles
     * 
     * @since    0.9.0
     * @return   void
     */
    public function styles_lib(): void
    {
        wp_enqueue_style('irm_lib_centum', IRM_WP_NOTIF_BELL_URL . 'assets/lib/centum/centum.min.css', false, '1.0.4');
    }

    /**
     * ltr styles files
     * 
     * @since    0.9.0
     * @return   void
     */
    public function styles_ltr(): void
    {
        wp_enqueue_style('wpnb_core_ltr', IRM_WP_NOTIF_BELL_URL . 'assets/dist/css/admin.css?refID=98hgf321', false, IRM_WP_NOTIF_BELL_VER);
    }

    /**
     * rtl styles files
     * 
     * @since    0.9.0
     * @return   void
     */
    public function styles_rtl(): void
    {
        wp_enqueue_style('wpnb_core_rtl', IRM_WP_NOTIF_BELL_URL . 'assets/dist/css/admin.rtl.css?refID=9fggfre1', false, IRM_WP_NOTIF_BELL_VER);
    }

    /**
     * check for scrren id
     * 
     * @see      https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     * @see      str_ends_with
     * 
     * @since    0.9.0
     * @param    string     $screen_id
     * @param    string     $id
     * @return   bool
     */
    private function is_screen_id(string $screen_id, string $id): bool
    {
        $length = strlen($id);
        if (!$length) {
            return true;
        }

        return substr($screen_id, -$length) == $id;
    }

    /**
     * main scripts
     * 
     * @since    0.9.0
     * @return   void
     */
    public function scripts(): void
    {
        wp_enqueue_script('wpnb_admin_scr', IRM_WP_NOTIF_BELL_URL . 'assets/dist/js/admin.js?refID=gwwew2323', ['jquery'], IRM_WP_NOTIF_BELL_VER, true);

        wp_localize_script('wpnb_admin_scr', 'wpnb_texts', $this->get_js_texts());

        $current_screen = get_current_screen();
        if (is_null($current_screen)) {
            return;
        }

        $id = $current_screen->id ?? '';

        wp_enqueue_script('wpnb_send_ut_hashjs', IRM_WP_NOTIF_BELL_URL . 'assets/dist/utils/hash.min.js', [], '1.7.8', true);

        if ($this->is_screen_id($id, 'wpnb-send')) {
            wp_enqueue_script('wpnb_send_scr', IRM_WP_NOTIF_BELL_URL . 'assets/dist/js/send-adm.js?refID=f3522463', ['jquery'], IRM_WP_NOTIF_BELL_VER, true);

            // main wpnb object
            wp_localize_script('wpnb_send_scr', 'wpnb_data_obj', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('wpnb_nnc_send')
            ]);
        }
        
        if ($this->is_screen_id($id, 'wpnb-settings')) {
            wp_enqueue_script('wpnb_send_settings', IRM_WP_NOTIF_BELL_URL . 'assets/dist/js/settings.js?refID=435546', ['jquery'], IRM_WP_NOTIF_BELL_VER, true);
        }
    }

    /**
     * [i18n]
     * get list of text for js files
     * 
     * @since   0.9.0
     * @return  array
     */
    private static function get_js_texts(): array
    {
        $texts = [
            'plh.user-id' => __('The user\'s ID (exa: 3724) [int]', 'wp-notif-bell'),
            'plh.user-name' => __('The user\'s Login (exa: nova) [str]', 'wp-notif-bell'),
            'plh.user-mail' => __('The user\'s Mail (exa: h@my.com) [e-mail]', 'wp-notif-bell'),
            'plh.user-role' => __('The user\'s Role [wp-role]', 'wp-notif-bell'),
            'plh.user-command' => __('The Command name (exa: all) [str]', 'wp-notif-bell'),
        ];

        /**
         * filter of i18n js texts
         * 
         * @since   0.9.0
         * @param   array   $texts   List of all texts
         */
        $texts = (array) apply_filters('wpnb_i18n_texts_js', $texts);

        return $texts;
    }
}