<?php

namespace Irmmr\WpNotifBell\Notif\Assist;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Data;

/**
 * Class Tags
 * implode and explode tags using separator
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Assist
 */
class Tags
{
    // @since   0.9.0
    public const SEPARATOR = ',';

    /**
     * clean mono tag
     * 
     * @since   0.9.0
     * @param   string $tag
     * @return  array
     */
    public static function clean_mono(string $tag): string
    {
        return Data::to_slug($tag);
    }

    /**
     * clean tags entry
     * 
     * @since   0.9.0
     * @param   array $tags
     * @return  array
     */
    public static function clean(array $tags): array
    {
        $tags = array_unique($tags);

        $tags = array_map(function ($tag) {
            return Data::to_slug($tag);
        }, $tags);

        $tags = array_filter($tags, function ($tag) {
            return !empty($tag);
        });

        return $tags;
    }

    /**
     * create tags string
     * 
     * @since   0.9.0
     * @param   array $tags
     * @return  string
     */
    public static function encode(array $tags): string
    {
        $tags = self::clean($tags);
        return implode(self::SEPARATOR, $tags);
    }

    /**
     * parse tags with separator
     * 
     * @since   0.9.0
     * @param   string $tags
     * @return  array
     */
    public static function parse(string $tags): array
    {
        $parse = explode(self::SEPARATOR, $tags);

        return self::clean($parse);
    }
}