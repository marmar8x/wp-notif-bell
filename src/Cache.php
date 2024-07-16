<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Data;
use Irmmr\WpNotifBell\Helpers\Fs;

/**
 * Class Cache
 * cache manager
 * 
 * ! I don't use nested folders anymore. I think this is redundant for this plugin.
 * ! Due to its simplicity, formatting and quick support are not used.
 *
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class Cache
{
    /**
     * The main folder path that used as cache storage
     * 
     * wp-content:
     * ABSPATH . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR
     * 
     * @since   0.9.0
     * @var     string  $storage_path   
     */
    private static string $storage_path = IRM_WPNB_CACHE_PATH;

    /**
     * The files extension. like file.EXT
     * default: file.wpnbc
     * 
     * @since   0.9.0
     * @var     string  $file_extension   
     */
    private static string $file_extension = 'wpnbc';

    /**
     * pre load [!!]
     * Creating some folders or otherwise when the plugin is activated.
     * ! Do not selectively run this yourself.
     * 
     * @since   0.9.0
     * @return  void
     */
    public static function preload(): void
    {
        Logger::add('cache preload started.');

        // create storage folder for first time
        if (!Fs::dir_exists(self::$storage_path)) {
            $creating_storage = Fs::mkdir(self::$storage_path);

            Logger::add("trying to create cache storage path.", Logger::N_MAIN, Logger::LEVEL_LOG, [
                'dir'       => self::$storage_path,
                'result'    => $creating_storage
            ]);

            if (!$creating_storage) {
                Logger::add('could not create cache storage folder.', Logger::N_MAIN, Logger::LEVEL_ERROR);
            }
        }

        Logger::add('cache preload ended.');
    }

    /**
     * get cache file real path.
     * 
     * @since   0.9.0
     * @param   string  $name   The name of cache file
     * @param   string  $stack  Stack name. stack/file
     * @return  string
     */
    public static function get_path(string $name, string $stack = ''): string
    {
        $path_array = [
            self::$storage_path,
            $stack,
            $name . '.' . self::$file_extension
        ];
        
        return Data::join_path($path_array);
    }
    
    /**
     * insert cache data and create file.
     * 
     * @since   0.9.0
     * @param   string  $name   The name of cache file
     * @param   string  $data   The content to insert in file.
     * @param   string  $stack  Stack name. stack/file
     * @return  void
     */
    public static function set(string $name, string $data, string $stack = ''): void
    {
        $file_path = self::get_path($name, $stack);

        Fs::write($file_path, $data);
    }

    /**
     * get cache file data.
     * 
     * @since   0.9.0
     * @param   string  $name   The name of cache file
     * @param   string  $stack  Stack name. stack/file
     * @return  string
     */
    public static function get(string $name, string $stack = ''): string
    {
        $file_path = self::get_path($name, $stack);

        return Fs::read($file_path);
    }

    /**
     * check if cache file exists.
     * 
     * @since   0.9.0
     * @param   string  $name   The name of cache file
     * @param   string  $stack  Stack name. stack/file
     * @return  bool
     */
    public static function defined(string $name, string $stack = ''): bool
    {
        $file_path = self::get_path($name, $stack);

        return Fs::file_exists($file_path);
    }
}