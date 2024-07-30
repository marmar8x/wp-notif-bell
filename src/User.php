<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Module\QuerySelector;
use Irmmr\WpNotifBell\Notif\Module\Database;

/**
 * Class User
 * include user data
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class User
{
    // seen list meta key name
    // @since 0.9.0
    protected const SEEN_META_KEY = 'wpnb_seen_list';

    // @since 0.9.0
    protected const SEEN_SEPARATOR = ','; 

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
     * @since   0.9.0
     * @param   int $user_id
     * @return  array
     */
    public static function get_seen_list(int $user_id): array
    {
        $list = strval( get_user_meta($user_id, self::SEEN_META_KEY, true) );

        if (empty($list)) {
            return [];
        }

        return explode(self::SEEN_SEPARATOR, $list);
    }

    /**
     * add notif id to seen list
     * 
     * @since   0.9.0
     * @param   int     $notif_id
     * @param   int     $user_id
     * @return  bool
     */
    public static function add_seen_list(int $notif_id, int $user_id): bool
    {
        $old_list = self::get_seen_list($user_id);

        if (in_array($notif_id, $old_list)) {
            return true;
        }

        $old_list[] = $notif_id;

        $value = implode(self::SEEN_SEPARATOR, $old_list);

        // [Debug] runs only when debugging
        if (Container::$debugging) {
            Logger::add("adding notif #{$notif_id} to seen list of user #{$user_id}", Logger::N_DEBUG, Logger::LEVEL_LOG, [
                'user_id'  => $user_id,
                'notif_id' => $notif_id
            ]);
        }

        return update_user_meta($user_id, self::SEEN_META_KEY, $value);
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
    public static function in_seen_list(int $notif_id, int $user_id): bool
    {
        /*
         * To prevent decrease server usage i don't use
         * this part. (huge data)

        $list = self::get_seen_list($user_id);

        foreach ($list as $id) {
            if ($id == $notif_id) {
                return true;
            }
        }

        return false;
        */

        global $wpdb;

        // get database prefix
        $db_prefix  = $wpdb->prefix;

        // get `usermeta` table name
        $user_meta_table = $db_prefix . 'usermeta';

        // create new mysql query
        $query = new QuerySelector($user_meta_table);

        // user query selector
        // trying to search in seen list for notif id
        // $notif_id is a safe entered integer
        $query->selector()
            ->count()
            ->where()
                ->equals('meta_key', self::SEEN_META_KEY)
                ->equals('user_id', $user_id)
                ->asLiteral('find_in_set('.$notif_id.',wp_usermeta.meta_value)')
                ->end();

        // get database result
        $db = new Database;
        $result = intval( $db->get_var($query->get(), $query->get_builder_values()) );

        return $result > 0;
    }
}