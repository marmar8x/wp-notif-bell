<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Settings;

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
        $this->styles( is_rtl() );

        $this->scripts();
    }

    /**
     * styles files
     * 
     * @since    0.9.0
     * @param    bool   $is_rtl
     * @return   void
     */
    public function styles(bool $is_rtl = false): void
    {
        wp_enqueue_style('irm_lib_centum', IRM_WP_NOTIF_BELL_URL . 'assets/lib/centum/centum.min.css', false, '1.0.4');

        if ($is_rtl) {
            wp_enqueue_style('wpnb_core_rtl', IRM_WP_NOTIF_BELL_URL . 'assets/dist/css/admin.rtl.css?refID=932fegfre1', false, IRM_WP_NOTIF_BELL_VER);
        } else {
            wp_enqueue_style('wpnb_core_ltr', IRM_WP_NOTIF_BELL_URL . 'assets/dist/css/admin.css?refID=98hge21', false, IRM_WP_NOTIF_BELL_VER);
        }

        $current_screen = get_current_screen();
        if (is_null($current_screen)) {
            return;
        }

        $id = $current_screen->id ?? '';

        // send page styles
        if ($this->is_screen_id($id, 'wpnb-send')) {
            if (is_rtl()) {
                wp_enqueue_style('wpnb_lib_quill_snow_rtl', IRM_WP_NOTIF_BELL_URL . 'assets/lib/quill/quill.snow.rtl.css?refID=345', false, '2.0.2');
                wp_enqueue_style('wpnb_lib_quill_bubble_rtl', IRM_WP_NOTIF_BELL_URL . 'assets/lib/quill/quill.bubble.rtl.css?refID=352', false, '2.0.2');
            } else {
                wp_enqueue_style('wpnb_lib_quill_snow', IRM_WP_NOTIF_BELL_URL . 'assets/lib/quill/quill.snow.css?refID=345', false, '2.0.2');
                wp_enqueue_style('wpnb_lib_quill_bubble', IRM_WP_NOTIF_BELL_URL . 'assets/lib/quill/quill.bubble.css?refID=352', false, '2.0.2');
            }

            wp_enqueue_style('wpnb_lib_codemirror', IRM_WP_NOTIF_BELL_URL . 'assets/lib/codemirror/codemirror.min.css?refID=332f34', false, '6.65.7');
        }
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
        wp_enqueue_script('wpnb_admin_scr', IRM_WP_NOTIF_BELL_URL . 'assets/dist/js/admin.js?refID=gww32f3', ['jquery'], IRM_WP_NOTIF_BELL_VER, true);

        wp_localize_script('wpnb_admin_scr', 'wpnb_texts', $this->get_js_texts());

        $current_screen = get_current_screen();
        if (is_null($current_screen)) {
            return;
        }

        $id = $current_screen->id ?? '';

        wp_enqueue_script('wpnb_ut_hashjs', IRM_WP_NOTIF_BELL_URL . 'assets/dist/utils/hash.min.js', [], '1.7.8', true);

        if ($this->is_screen_id($id, 'wpnb-send')) {
            // quill editor
            wp_enqueue_script('wpnb_lib_quill', IRM_WP_NOTIF_BELL_URL . 'assets/lib/quill/quill.js?refID=23few4', [], '2.0.2', true);

            // codemirror
            wp_enqueue_script('wpnb_lib_codemirror', IRM_WP_NOTIF_BELL_URL . 'assets/lib/codemirror/codemirror.bundle.js?refID=f325648563', [], '6.65.7', false);

            // markdown parser js -> marked
            wp_enqueue_script('wpnb_lib_marked', IRM_WP_NOTIF_BELL_URL . 'assets/lib/marked/marked.min.js?refID=f123754263', [], '13.0.2', true);

            // send script
            wp_enqueue_script('wpnb_send_scr', IRM_WP_NOTIF_BELL_URL . 'assets/dist/js/send-adm.js?refID=fafw23g3', ['jquery'], IRM_WP_NOTIF_BELL_VER, true);

            // main wpnb object
            wp_localize_script('wpnb_send_scr', 'wpnb_data_obj', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('wpnb_nnc_send'),
                'dir'      => is_rtl() ? 'rtl' : 'ltr',
                'quill'    => [
                    'theme' => Settings::get('admin.ui.quill_theme', 'snow')
                ]
            ]);
        }
        
        if ($this->is_screen_id($id, 'wpnb-settings')) {
            wp_enqueue_script('wpnb_send_settings', IRM_WP_NOTIF_BELL_URL . 'assets/dist/js/settings.js?refID=4w5546', ['jquery'], IRM_WP_NOTIF_BELL_VER, true);
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
            'err.res-name'        => __('The reseiver has no name!', 'wp-notif-bell'),
            'err.res-name-fr'     => __('Receiver name is unvalid! Use (a-zA-Z0-9) and "-" for enter receiver name.', 'wp-notif-bell'),
            'err.res-data'        => __('Please fill the data field first.', 'wp-notif-bell'),
            'err.res-dup'         => __('The receiver is duplicate!', 'wp-notif-bell'),
            'err.res-dup'         => __('The receiver is duplicate!', 'wp-notif-bell'),
            'incorrect'           => __('Incorrect', 'wp-notif-bell'),
            'notif.text-pl'       => __('Notif Text ...', 'wp-notif-bell'),
            'editor.md-pl'        => __('Markdown here ...', 'wp-notif-bell'),
            'editor.ht-pl'        => __('HTML here ...', 'wp-notif-bell'),
            'err.n-sending'       => __('Please wait until the notif is sent.', 'wp-notif-bell'),
            'err.n-sender-req'    => __('Notif sender is required!', 'wp-notif-bell'),
            'err.n-title-req'     => __('Notif title is required!', 'wp-notif-bell'),
            'err.n-text-req'      => __('Can not send notif without content!', 'wp-notif-bell'),
            'err.n-format-req'    => __('Please select notif content format!', 'wp-notif-bell'),
            'err.n-res-req'       => __('You can not send notif without any receiver.', 'wp-notif-bell'),
            'err.n-date'          => __('Notif date must entered as yyyy-mm-dd hh:ii:ss or just leave it blank for auto fill.', 'wp-notif-bell'),
            'err.fix-msg'         => __('Please fix the following error(s):', 'wp-notif-bell'),
            'msg.sending'         => __('Sending notification ...', 'wp-notif-bell'),
            'msg.updating'        => __('Updating notification ...', 'wp-notif-bell'),
            'res.updated'         => __('Result: The notification was updated.', 'wp-notif-bell'),
            'msg.update-f'        => __('Result: An error occurred while updating.', 'wp-notif-bell'),
            'res.sent'            => __('Result: The notification was sent.', 'wp-notif-bell'),
            'msg.send-f'          => __('Result: An error occurred while sending.', 'wp-notif-bell'),

            'err.code'            => __('Error code:', 'wp-notif-bell'),
            'err.msg'             => __('Error msg:', 'wp-notif-bell'),
            'errors'              => __('Errors', 'wp-notif-bell'),
            'data'                => __('Data', 'wp-notif-bell'),
            'su.result'           => __('Sender/Updater result:', 'wp-notif-bell'),
            'btn.n-edit'          => __('Edit notif', 'wp-notif-bell'),
            'emp-auto'            => __('Empty: Auto fill', 'wp-notif-bell'),
            'optional'            => __('Empty: Auto fill', 'wp-notif-bell'),
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