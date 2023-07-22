<?php

namespace Irmmr\WpNotifBell\Notif;

/**
 * Class Token
 * create data tokens to prevent data modification
 * in database columns
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif
 */
class Token
{
    /**
     * a sha-256 hash string for build tokens
     * 
     * @since   0.9.0
     * @var     string
     */
    private static string $hash = 'sha256-needed';

    /**
     * create a data token
     * 
     * @since   0.9.0
     * @param   array $data
     * @return  string
     */
    public static function create(array $data): string
    {
        $data_string = json_encode($data);
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