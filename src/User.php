<?php

namespace Irmmr\WpNotifBell;

/**
 * Class User
 * include user data
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class User
{
    /**
     * get user data by user id
     * 
     * @since   0.9.0
     * @param   int|WP_User     $user_id
     * @return  WP_User|null
     */
    public static function get_data($user): ?\WP_User
    {
        if ($user instanceof \WP_User) {
            return $user;
        } else if (is_int($user)) {
            return get_userdata($user);
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
     * get user name by user id
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
     * get user name by user id
     * 
     * @since   0.9.0
     * @param   int|WP_User $user
     * @return  array<{ name, data }>
     */
    public static function get_identity($user): array
    {
        $user_data  = self::get_data($user);

        if (is_null($user_data)) {
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
}