<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Data;
use Irmmr\WpNotifBell\Helpers\Date;
use Irmmr\WpNotifBell\Helpers\Fs;

/**
 * Class Logger
 * craete logger and insert logs
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class Logger
{
    // @since 0.9.0
    public const DIR = 'storage/logs';

    // @since 0.9.0
    public const N_MAIN = 'main';

    // @since 0.9.0
    public const LEVEL_LOG      = 'LOG';
    public const LEVEL_WARN     = 'WARN';
    public const LEVEL_ERROR    = 'ERROR';

    /**
     * @since   0.9.0
     * @var     array
     */
    public static array $levels = [
        self::LEVEL_LOG,
        self::LEVEL_WARN,
        self::LEVEL_ERROR
    ];

    /**
     * @since   0.9.0
     * @var     string
     */
    public static string $extension = '.txt';

    /**
     * @since   0.9.0
     * @var     string
     */
    public static string $structure = '[%s] (%s) %s %s';

    /**
     * check for a valid logger level
     * 
     * @since   0.9.0
     * @param   string  $level
     * @return  bool
     */
    private static function is_valid_level(string $level): bool
    {
        return in_array($level, self::$levels);
    }

    /**
     * encode data array for msg log
     * 
     * @since   0.9.0
     * @param   array  $data
     * @return  string
     */
    private static function encode_data(array $data): string
    {
        return empty($data) ? '' : json_encode($data);
    }

    /**
     * build a line logger text for insert
     * 
     * @since   0.9.0
     * @param   string  $msg
     * @param   string  $level
     * @param   array   $data
     * @return  string
     */
    private static function build_log_txt(string $msg, string $level, array $data): string
    {
        $date = Date::by_format('Y-m-d H:i:s');

        return sprintf(self::$structure, $date, $level, $msg, self::encode_data($data));
    }

    /**
     * get log file path
     * 
     * @since   0.9.0
     * @param   string  $name
     * @return  string
     */
    public static function get_path(string $name): string
    {
        return Data::join_path([IRM_WP_NOTIF_BELL_PTH, self::DIR, $name . self::$extension]);
    }

    /**
     * add a log to logger files
     * 
     * @since   0.9.0
     * @param   string  $msg
     * @param   string  $name
     * @param   string  $level
     * @param   array   $data
     * @return  bool
     */
    public static function add(string $msg, string $name = self::N_MAIN, string $level = self::LEVEL_LOG, array $data = []): bool
    {
        if (!self::is_valid_level($level) || empty($name)) {
            return false;
        }

        $name = Data::to_slug($name);
        $path = self::get_path($name);
        $text = self::build_log_txt($msg, $level, $data);

        return Fs::append($path, $text . PHP_EOL);
    }
}