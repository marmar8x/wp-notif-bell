<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Data
 * data helpers that includes basic ones
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Helpers
 */
class Data
{
    /**
     * join path to build path
     * 
     * @since   0.9.0
     * @param   array   $path
     * @param   string  $seprator
     * @return  string
     */
    public static function join_path(array $path, string $seprator = DIRECTORY_SEPARATOR): string
    {
        $path  = array_filter($path, function ($i) {
            return !empty($i);
        });
        $build = implode($seprator, $path);

        $replaces   = $seprator === '\\' ? ['/'] : [];
        $replaces[] = str_repeat($seprator, 2);

        $build = str_replace($replaces, $seprator, $build);

        return $build;
    }

    /**
     * convert a string to a slug
     * 
     * @since   0.9.0
     * @param   string  $str
     * @param   string  $delimiter
     * @return  string
     */
    public static function to_slug(string $str, string $delimiter = '-'): string
    {
        return strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    }

    /**
     * clean string data for db insert
     * 
     * @since   0.9.0
     * @param   string  $data
     * @return  string
     */
    public static function clean_db(string $data): string
    {
        return trim( addslashes( htmlspecialchars($data) ) );
    }

    /**
     * create random string
     * 
     * @since   0.9.0
     * @param   int $length
     * @return  string
     */
    public static function random_str(int $length = 10): string
    {
        $characters         = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength   = strlen($characters);
        $randomString       = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * check for valid json
     * 
     * @since   0.9.0
     * @param   string $str
     * @return  bool
     */
    public static function is_json(string $str): bool
    {
        json_decode($str);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * check if string is a formatted date/time
     * 
     * @since   0.9.0
     * @param   string  $str
     * @return  bool
     */
    public static function is_datetime(string $str): bool
    {
        return strlen($str) === 19 && strtotime($str) !== false;
    }
}