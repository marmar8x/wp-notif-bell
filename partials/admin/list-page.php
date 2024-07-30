<?php
/**
 * Partial: admin list page
 * view all notifications with
 * a unique list address
 * 
 * @since    0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Admin\NotifList;

// use notification list to show data
$_notif_list = new NotifList;

?>

<div class="wpnb-ad-box">
    <div class="wpnb-ad-header">
        <h3 class="wpnb-ad-header-title">
            🔔 <?php _e('WP Notif Bell', 'wp-notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org"><?php _e('List', 'wp-notif-bell'); ?></span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php _e('List of all notifications', 'wp-notif-bell'); ?>
        </p>
    </div>

    <div class="wpnb-w-100">
        <?php $_notif_list->render(); ?>
    </div>

</div>
