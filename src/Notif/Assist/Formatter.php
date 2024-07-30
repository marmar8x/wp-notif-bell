<?php

namespace Irmmr\WpNotifBell\Notif\Assist;

// If this file is called directly, abort.
defined('WPINC') || die;

use stdClass;

/**
 * Class Formatter
 * clean and render all notif fields
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Assist
 */
class Formatter
{
    /**
     * encode database array data for insert
     * 
     * @since   0.9.0
     * @param   array   $data
     * @return  array
     */
    public static function encode(array $data): array
    {
        if (isset($data['content'])) {
            $data['content'] = htmlentities( $data['content'] );
        }

        return $data;
    }

    /**
     * decode database array data for fetch
     * 
     * @since   0.9.0
     * @param   stdClass   $data
     * @return  stdClass
     */
    public static function decode(stdClass $data): stdClass
    {
        if (isset($data->content)) {
            $data->content = html_entity_decode(htmlspecialchars_decode( $data->content ));
            $data->content = wp_unslash($data->content);

            $data->title  = wp_unslash($data->title);
        }

        return $data;
    }
}