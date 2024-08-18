<?php

namespace Irmmr\WpNotifBell\Notif\Assist;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Token
 * create data tokens to prevent data modification
 * in database columns
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Assist
 */
class Token
{
    /**
     * a sha-256 hash string for build tokens
     * 
     * @since   0.9.0
     * @var     string
     */
    private static string $hash = NONCE_KEY ?? 'sha256-needed';

    /**
     * create a data token
     * 
     * @since   0.9.0
     * @param   array $data
     * @return  string
     */
    public static function create(array $data): string
    {
        $data_string = wp_json_encode($data);
        $hash        = self::$hash;

        return hash('sha256', "data:{$data_string};hash:{$hash};");
    }

    /**
     * verify a data token
     * 
     * @since   0.9.0
     * @param   array   $data
     * @param   string  $token
     * @return  bool
     */
    public static function verify(array $data, string $token): bool
    {
        return self::create($data) === $token;
    }
}