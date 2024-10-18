<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Data;

/**
 * Class Partial
 * load and get partial contents
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class Partial
{
    // @since 0.9.0
    public const DIR_NAME = 'partials';

    /**
     * get a partial full path to file
     * 
     * @since   0.9.0
     * @param   string  $name
     * @param   array   $subs
     * @return  string
     */
    public static function get_path(string $name, array $subs = []): string
    {
        $file = Data::join_path( array_merge($subs, [$name . '.php']) );
        return Data::join_path([MM8X_WP_NOTIF_BELL_PTH, self::DIR_NAME, $file]);
    }

    /**
     * check a partial
     * 
     * @since   0.9.0
     * @param   string  $name
     * @param   array   $subs
     * @return  bool
     */
    public static function check(string $name, array $subs = []): bool
    {
        $path = self::get_path($name, $subs);

        return file_exists($path) && is_readable($path);
    }

    /**
     * require a partial
     * 
     * @since   0.9.0
     * @param   string  $name
     * @param   array   $subs
     * @return  void
     */
    public static function req(string $name, array $subs = []): void
    {
        $path = self::get_path($name, $subs);

        if (self::check($name, $subs)) {
            require $path;
        }
    }
}