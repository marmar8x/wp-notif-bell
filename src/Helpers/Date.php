<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

use DateTime;

/**
 * Class Date
 * date system for plugin based on
 * wordpress timezone
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Helpers
 */
class Date
{
    /**
     * create date using wp_timezone
     * 
     * @since   0.9.0
     * @param   string  $format
     * @return  string
     */
    public static function by_format(string $format): string
    {
        $wp_date = new DateTime('now', wp_timezone());
        return $wp_date->format($format);
    }

    /**
     * convert date to i18n
     * 
     * @since   0.9.0
     * @param   string   $time
     * @return  string   $format
     */
    public static function to_i18n(string $time, string $format = 'Y-m-d H:i:s'): string
    {
        $time = strtotime($time);

        if (!is_int($time)) {
            return '';
        }

        return date_i18n($format, $time);
    }
}