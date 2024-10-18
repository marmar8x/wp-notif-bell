<?php
/**
 * Partial: admin main page
 * admin menu content file
 * 
 * @since    0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

?>

<div class="wpnb-ad-box">
    <div class="wpnb-ad-header">
        <h3 class="wpnb-ad-header-title">
            🔔 <?php esc_html_e('WP Notif Bell', 'notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org">WPNB</span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php esc_html_e('User notification bell for wordpress', 'notif-bell'); ?>
        </p>
    </div>

    <div class="wpnb-w-100" style="line-height: 1.7rem;font-size: 1.0rem;">
        <?php esc_html_e('This plugin is designed for implementing the notification section of WordPress. The purpose of this plugin is to create a suitable platform for designing the display section of user-specific notifications for each user.', 'notif-bell'); ?>
        <br />
        <?php esc_html_e('This plugin does not automatically place a bell or notification display area in the user section, and you cannot use it simply by installing the plugin without initial implementation.', 'notif-bell'); ?>
        <br />
        <?php esc_html_e('This plugin can make the task of displaying notifications very easy for you. You just need to use a bit of HTML and CSS along with the helper functions of the plugin to execute everything.', 'notif-bell'); ?>
    </div>

    <div class="wpnb-ad-l-box jst">
        <p class="wpnb-txt-block">
            <?php esc_html_e('The plugin consists of 4 main sections: `sender`, `collector`, `updater`, and `remover`.', 'notif-bell'); ?>
            <?php esc_html_e('These sections essentially play the roles of `Insert`, `Select`, `Update`, and `Delete` for the database.', 'notif-bell'); ?>
            <?php /* translators: %s: page link */ ?>
            <?php printf( esc_html__('All four main sections of the plugin have been designed and implemented with support for %s, and you can easily use this library as well.', 'notif-bell'), '<b><a href="https://github.com/nilportugues/php-sql-query-builder">PHP Sql Query Builder</a></b>' ); ?>
        </p>
    </div>
    <p>
        <?php esc_html_e('For more information and how to use it, be sure to check the GitHub page of the plugin:', 'notif-bell'); ?>
        <a href="https://github.com/marmar8x/wp-notif-bell">https://github.com/marmar8x/wp-notif-bell</a>
    </p>
</div>
