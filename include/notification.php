<?php
/**
 * Notification file
 * init main notif functions
 * 
 * @since   0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Container;
use Irmmr\WpNotifBell\Notif\Collector;
use Irmmr\WpNotifBell\Notif\Instance\Data;
use Irmmr\WpNotifBell\Notif\Instance\Notification;
use Irmmr\WpNotifBell\Notif\Instance\Receiver;
use Irmmr\WpNotifBell\Notif\Remover;
use Irmmr\WpNotifBell\Notif\Sender;
use Irmmr\WpNotifBell\Notif\Updater;

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
 * @return  Collector
 */
function wpnb_collector(): Collector
{
    return new Collector;
}

/**
 * main notif remover
 * 
 * @since   0.9.0
 * @return  Remover
 */
function wpnb_remover(): Remover
{
    return new Remover;
}

/**
 * main notif updater
 * 
 * @since   0.9.0
 * @return  Updater
 */
function wpnb_updater(): Updater
{
    return new Updater;
}

/**
 * receiver class for sender
 * 
 * @since   0.9.0
 * @param   string $name
 * @param   string $data
 * @return  Receiver
 */
function wpnb_receiver_ins(string $name, string $data): Receiver
{
    return new Receiver($name, $data);
}

/**
 * data instance for notifs
 * 
 * @since   0.9.0
 * @param   string  $key
 * @param   mixed   $value
 * @return  Data
 */
function wpnb_data_ins(string $key, $value): Data
{
    return new Data($key, $value);
}

/**
 * get notifs for user
 * 
 * @since   0.9.0
 * @param   int|WP_User $user
 * @param   array       $options
 * @return  array
 */
function wpnb_get_by_user($user, array $options = []): array
{
    $collect = wpnb_collector();
    $collect->target_by_user($user);
    $collect->config($options);

    return $collect->fetch();
}

/**
 * get notifs for user
 * 
 * @since   0.9.0
 * @param   string  $command
 * @param   array   $options
 * @return  array
 */
function wpnb_get_by_command(string $command, array $options = []): array
{
    $collect = wpnb_collector();
    $collect->target( wpnb_receiver_ins('command', $command) );
    $collect->config($options);

    return $collect->get();
}

/**
 * get notifs
 * 
 * @since   0.9.0
 * @param   array   $options
 * @return  array
 */
function wpnb_get(array $options = []): array
{
    $collect = wpnb_collector();
    $collect->config($options);

    return $collect->get();
}

/**
 * check if result is a notif
 * 
 * @since   0.9.0
 * @param   mixed   $result
 * @return  bool
 */
function wpnb_is_notif($result): bool
{
    return $result instanceof Notification;
}

/**
 * get all text formats
 * 
 * @since   0.9.0
 * @return  array
 */
function wpnb_get_text_formats(): array
{
    return Container::$text_formats;
}
