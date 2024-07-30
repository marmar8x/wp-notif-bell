<?php

namespace Irmmr\WpNotifBell\Notif\Assist;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Notif\Instance\Data as InstanceData;

/**
 * Class Data
 * serialize with data
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Assist
 */
class Data
{
    /**
     * check data
     * 
     * @since   0.9.0
     * @param   string|null $data
     * @return  bool
     */
    public static function is_valid(?string $data): bool
    {
        return !empty($data) && is_serialized($data);
    }

    /**
     * create data serialized string
     * 
     * @since   0.9.0
     * @param   array $data
     * @return  string|null
     */
    public static function encode(array $data): ?string
    {
        if (empty($data)) {
            return null;
        }

        // get only key and value from data list
        $fetch = [];

        foreach ($data as $d) {
            if ($d instanceof InstanceData && $d->is_valid()) {
                $fetch[ $d->get_key() ] = $d->get_value();
            }
        }

        return strval( maybe_serialize($fetch) );
    }

    /**
     * unserialize data
     * 
     * @since   0.9.0
     * @param   null|string     $data
     * @return  array
     */
    public static function parse(?string $data): array
    {
        if (!self::is_valid($data)) {
            return [];
        }

        $unserialize = maybe_unserialize($data);

        return is_array($unserialize) ? $unserialize : [];
    }
}