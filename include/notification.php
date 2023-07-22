<?php
/**
 * Notification file
 * init main notif functions
 * 
 * @since   0.9.0
 */

use Irmmr\WpNotifBell\Notif\Collector;
use Irmmr\WpNotifBell\Notif\Receiver;
use Irmmr\WpNotifBell\Notif\Sender;

/**
 * main notif sender
 * 
 * @since   0.9.0
 * @return  Sender
 */
function wpnb_sender(): Sender
{
    return new Sender;
}

/**
 * main notif collector
 * 
 * @since   0.9.0
 * @param   int|WP_User|array   $target     user-id | user | list of receivers like: [[ 'name', 'data' ]]
 * @return  Collector
 */
function wpnb_collector($target): Collector
{
    return new Collector($target);
}

/**
 * receiver class for sender
 * 
 * @since   0.9.0
 * @param   string $name
 * @param   string $data
 * @return  Receiver
 */
function wpnb_receiver(string $name, string $data): Receiver
{
    return new Receiver($name, $data);
}
