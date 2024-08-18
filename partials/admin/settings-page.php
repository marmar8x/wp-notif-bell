<?php
/**
 * Partial: admin settings page
 * admin about page content
 * 
 * @since    0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

// get setting page tab
$_section = $_GET['section'] ?? '';

?>

<div class="wpnb-ad-box">
    <div class="wpnb-ad-header">
        <h3 class="wpnb-ad-header-title">
            🔔 <?php esc_html_e('WP Notif Bell', 'wp-notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org"><?php esc_html_e('Settings', 'wp-notif-bell'); ?></span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php esc_html_e('Plugin settings', 'wp-notif-bell'); ?>
        </p>
    </div>

    <?php wpnb_settings_msg(); ?>

    <?php wpnb_render_settings($_section); ?>
</div>
