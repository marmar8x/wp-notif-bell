<?php
/**
 * Partial: admin about page
 * admin about page content
 * 
 * @since    0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

?>

<div class="wpnb-ad-box">
    <div class="wpnb-ad-header">
        <h3 class="wpnb-ad-header-title">
            🔔 WP Notif Bell
            <span class="wpnb-ad-header-tab rnd org">About</span>
        </h3>

        <p class="wpnb-ad-header-text">
            About this plugin and some notes
        </p>
    </div>

    <div class="wpnb-ad-l-box jst">
        <p class="wpnb-txt-block">
            You can use this plugin to implement user notification system for your WordPress site. With this plugin, different notifications can be sent to different users and roles. This plugin is completely free and open source.
            <br />
            Note that you cannot use this plugin just by installing it, but it must be implemented by another skin or plugin. This plugin is mostly made to help the development of the user notification system and requires an initial preparation by the designer. The least you need to do to get started is to design a section or element to display notifications that are not displayed by default by the plugin.
        </p>

        <p class="wpnb-txt-block mx">
            📨 <b>Sender:</b>
            To send or save a notification, its recipients must be specified.
            Recipients can be specified by `user-name`, `user-role`, `user-mail`, `user-id` and some commands like `all` for all users.
            You can use PHP functions or the submission form to send.
        </p>

        <div class="pack:no-gutters cell:100">
            <div class="tier content:center">
                <div class="cell:100 cell-sm:50">
                    <div class="wpnb-ad-d-box wpnb-w-100" style="font-size: 1.2rem;">
                        See <span style="color:blue;">Notif Bell -> Send</span>
                    </div>
                </div>

                <div class="cell:100 cell-sm:50">
                    <div class="wpnb-ad-d-box wpnb-w-100" style="font-size: 1.2rem;">
                        Use <span style="color:blue;">wpnb_sender</span><span style="color:chocolate;">()</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
