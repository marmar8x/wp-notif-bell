<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Fs
 * file writing and reading
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
}