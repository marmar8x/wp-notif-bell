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
            🔔 <?php esc_html_e('WP Notif Bell', 'notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org"><?php esc_html_e('List', 'notif-bell'); ?></span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php esc_html_e('List of all notifications', 'notif-bell'); ?>
        </p>
    </div>

    <div class="wpnb-w-100">
        <?php $_notif_list->render(); ?>
    </div>

</div>
