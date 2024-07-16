<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Fs
 * file writing and reading
 * 
 * - This class is used for planning the future and also for logging
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Helpers
 */
class Fs
{
    /**
     * write to a file
     * 
     * @since   0.9.0
     * @param   string  $path
     * @param   string  $data
     * @return  bool
     */
    public static function write(string $path, string $data): bool
    {
        return file_put_contents($path, $data) !== false;
    }

    /**
     * append to a file
     * 
     * @since   0.9.0
     * @param   string  $path
     * @param   string  $data
     * @return  bool
     */
    public static function append(string $path, string $data): bool
    {
        return file_put_contents($path, $data, FILE_APPEND) !== false;
    }

    /**
     * check file/dir exists
     * 
     * @since   0.9.0
     * @param   string  $path
     * @return  bool
     */
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * check file exists
     * 
     * @since   0.9.0
     * @param   string  $path
     * @return  bool
     */
    public static function file_exists(string $path): bool
    {
        return file_exists($path) && is_file($path);
    }

    /**
     * check dir exists
     * 
     * @since   0.9.0
     * @param   string  $path
     * @return  bool
     */
    public static function dir_exists(string $path): bool
    {
        return file_exists($path) && is_dir($path);
    }

    /**
     * read a file content
     * 
     * @since   0.9.0
     * @param   string  $path
     * @return  string
     */
    public static function read(string $path): string
    {
        return self::file_exists($path) ? file_get_contents($path) : '';
    }

    /**
     * create a directory
     * 
     * @see     https://www.php.net/manual/en/function.mkdir.php
     * 
     * @since   0.9.0
     * @param   string  $path
     * @param   int     $mode
     * @param   bool    $recursive
     * @return  string
     */
    public static function mkdir(string $path, int $mode = 0777, bool $recursive = false): string
    {
        if (!self::dir_exists($path)) {
            return mkdir($path, $mode, $recursive);
        }

        return false;
    }
}