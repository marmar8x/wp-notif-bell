<?php
/**
 * User file
 * define user classes functions.
 * 
 * @since   0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\User;

/**
 * get user ide
 * 
 * @since   0.9.0
 * @param   int|WP_User $user
 * @return  array<{ name, data }>
 */
function wpnb_user_get_ide($user): array
{
    return User::get_identity($user);
}

/**
 * get notifs seen list of user
 * 
 * @since   0.9.0
 * @param   int $user_id
 * @return  array<number>
 */
function wpnb_get_user_seen(int $user_id): array
{
    return User::get_seen_list($user_id);
}

/**
 * check if a notif seen by user.
 * ! using database selector instead of simple usermeta to
 * increase speed.
 * 
 * @see     Observer, Observer->apply
 * 
 * @since   0.9.0
 * @param   int     $notif_id
 * @param   int     $user_id
 * @return  bool
 */
function wpnb_is_user_seen(int $notif_id, int $user_id): bool
{
    return User::in_seen_list($notif_id, $user_id);
}

/**
 * add notif id to seen list
 * 
 * @since   0.9.0
 * @param   int     $notif_id
 * @param   int     $user_id
 * @return  bool
 */
function wpnb_user_add_seen(int $notif_id, int $user_id): bool
{
    return User::add_seen_list($notif_id, $user_id);
}
