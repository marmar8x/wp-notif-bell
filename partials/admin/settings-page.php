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
            🔔 WP Notif Bell
            <span class="wpnb-ad-header-tab rnd org">About</span>
        </h3>

        <p class="wpnb-ad-header-text">
            Settings are under development.
        </p>
    </div>

    <?php wpnb_settings_msg(); ?>

    <?php wpnb_render_settings($_section); ?>
</div>
