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
            🔔 WP Notif Bell
            <span class="wpnb-ad-header-tab rnd org">List</span>
        </h3>

        <p class="wpnb-ad-header-text">
            List of all notifications
        </p>
    </div>

    <div class="wpnb-w-100">
        <?php $_notif_list->render(); ?>
    </div>

</div>
