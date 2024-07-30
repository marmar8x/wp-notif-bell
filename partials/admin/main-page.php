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
            🔔 <?php _e('WP Notif Bell', 'wp-notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org">WPNB</span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php _e('User notification bell for wordpress', 'wp-notif-bell'); ?>
        </p>
    </div>

    <div class="wpnb-w-100" style="line-height: 1.7rem;font-size: 1.0rem;">
        <?php _e('This plugin is designed for implementing the notification section of WordPress. The purpose of this plugin is to create a suitable platform for designing the display section of user-specific notifications for each user.', 'wp-notif-bell'); ?>
        <br />
        <?php _e('This plugin does not automatically place a bell or notification display area in the user section, and you cannot use it simply by installing the plugin without initial implementation.', 'wp-notif-bell'); ?>
        <br />
        <?php _e('This plugin can make the task of displaying notifications very easy for you. You just need to use a bit of HTML and CSS along with the helper functions of the plugin to execute everything.', 'wp-notif-bell'); ?>
    </div>

    <div class="wpnb-ad-l-box jst">
        <p class="wpnb-txt-block">
            <?php _e('The plugin consists of 4 main sections: `sender`, `collector`, `updater`, and `remover`.', 'wp-notif-bell'); ?>
            <?php _e('These sections essentially play the roles of `Insert`, `Select`, `Update`, and `Delete` for the database.', 'wp-notif-bell'); ?>
            <?php echo sprintf( __('All four main sections of the plugin have been designed and implemented with support for %s, and you can easily use this library as well.', 'wp-notif-bell'), '<b><a href="https://github.com/nilportugues/php-sql-query-builder">PHP Sql Query Builder</a></b>' ); ?>
        </p>
    </div>
    <p>
        <?php _e('For more information and how to use it, be sure to check the GitHub page of the plugin:', 'wp-notif-bell'); ?>
        <a href="https://github.com/irmmr/wp-notif-bell">https://github.com/irmmr/wp-notif-bell</a>
    </p>
</div>
