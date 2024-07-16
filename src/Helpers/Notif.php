<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Container;

/**
 * Class Notif
 * some notif helpers
 *
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Helpers
 */
class Notif
{
    /**
     * check if format is valid
     * 
     * @since   0.9.0
     * @param   string $format  Text format
     * @return  bool
     */
    public static function is_valid_format(string $format): bool
    {
        $formats = Container::$text_formats;

        return array_key_exists($format, $formats);
    }
}