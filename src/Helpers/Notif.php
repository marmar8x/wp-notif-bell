<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Container;
use Irmmr\WpNotifBell\Notif\Collector;

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

    /**
     * get max notifications id
     *
     * @since   1.0.0
     * @return  int
     */
    public static function get_max_id(): int
    {
        $collector = new Collector;

        $collector->select()->setColumns([])->setFunctionAsColumn('MAX', ['id'], 'max_id');

        $result = $collector->get_results();

        if (!isset( $result[0] )) {
            return 0;
        }

        return intval( $result[0]->max_id ?? 0 );
    }
}