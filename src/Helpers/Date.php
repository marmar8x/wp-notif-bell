<?php

namespace Irmmr\WpNotifBell\Helpers;

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
}