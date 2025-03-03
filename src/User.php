<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Interfaces\UserInterface;
use Irmmr\WpNotifBell\User\Eye;
use WP_User;

/**
 * Class User
 * include user data
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class User implements UserInterface
{
    /**
     * get user data by user id
     *
     * @param   WP_User|int     $user
     * @return  WP_User|null
     * @since   0.9.0
     */
    public static function get_data($user): ?\WP_User
    {
        if ($user instanceof \WP_User) {
            return $user;
        } else if (is_int($user)) {
            $data = get_userdata($user);
            return $data !== false ? $data : null;
        } else {
            return null;
        }
    }

    /**
     * get user roles
     * 
     * @since   0.9.0
     * @param   int|WP_User $user
     * @return  array
     */
    public static function get_roles($user): array
    {
        $user_data = self::get_data($user);
        return (array) ($user_data->roles ?? []);
    }

    /**
     * get username by user id
     * 
     * @since   0.9.0
     * @param   int|WP_User $user
     * @return  string
     */
    public static function get_username($user): string
    {
        $user_data = self::get_data($user);
        return $user_data->user_login ?? '';
    }

    /**
     * get user ide
     * 
     * @since   0.9.0
     * @param   int|WP_User $user
     * @return  array<{ name, data }>
     */
    public static function get_identity($user): array
    {
        $user_data  = self::get_data($user);

        if (is_null($user_data)) {
            // [Debug] runs only when debugging
            if (Container::$debugging) {
                Logger::add('Error when targeting user', Logger::N_DEBUG, Logger::LEVEL_ERROR, [
                    'user' => $user
                ]);
            }
            
            return [];
        }

        $identity   = [
            [ 'name' => 'command', 'data' => 'all' ],
            [ 'name' => 'user-id', 'data' => (string) $user_data->ID ],
            [ 'name' => 'user-name', 'data' => (string) $user_data->user_login ],
            [ 'name' => 'user-mail', 'data' => (string) $user_data->user_email ],
        ];

        foreach ($user_data->roles as $role) {
            $identity[] = [ 'name' => 'user-role', 'data' => (string) $role ];
        }

        /**
         * filter of user identity for add customs or change existing ones
         * 
         * @since   0.9.0
         * @param   array   $identity   list of user identities that registered
         * @param   WP_User $user_data  the current user data
         */
        $identity = apply_filters('wpnb_user_identity', $identity, $user_data);

        return $identity;   
    }

    /**
     * get notifs seen list of user
     *
     * @deprecated  1.0.0   use Irmmr\WpNotifBell\User\Eye::get_seen
     *
     * @since   0.9.0
     * @param   int $user_id
     * @return  array<number>
     */
    public static function get_seen_list(int $user_id): array
    {
        $user = get_userdata($user_id);

        if ($user instanceof \WP_User) {
            return (new Eye( $user ))->get_seen();
        }

        return [];
    }

    /**
     * add notif id to seen list
     *
     * @deprecated  1.0.0   use Irmmr\WpNotifBell\User\Eye::set_seen
     *
     * @since   0.9.0
     * @param   int     $notif_id
     * @param   int     $user_id
     * @return  bool
     */
    public static function add_seen_list(int $notif_id, int $user_id): bool
    {
        $user = get_userdata($user_id);

        if ($user instanceof \WP_User) {
            (new Eye( $user ))->set_seen($notif_id);

            return true;
        }

        return false;
    }

    /**
     * check if a notif seen by user.
     * ! using database selector instead of simple usermeta to
     * increase speed.
     *
     * @see     Observer, Observer->apply
     * @deprecated  1.0.0   use Irmmr\WpNotifBell\User\Eye::get_status
     *
     * @since   0.9.0
     * @param   int     $notif_id
     * @param   int     $user_id
     * @return  bool
     */
    public static function in_seen_list(int $notif_id, int $user_id): bool
    {
        $user = get_userdata($user_id);

        if ($user instanceof WP_User) {
            $eye = new Eye( $user );
            return $eye->get_status($notif_id);
        }

        return false;
    }

    /**
     * get current/selected user eye
     *
     * @param   WP_User|null    $user
     * @param   array           $options
     * @return  Eye|null        null for failure
     * @since   1.0.0
     */
    public static function eye(?WP_User $user = null, array $options = []): ?Eye
    {
        $user = $user ?? get_userdata( get_current_user_id() );

        if (is_null($user)) {
            return null;
        }

        return new Eye($user, $options);
    }
}