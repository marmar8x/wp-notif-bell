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
    public const N_MAIN       = 'main';
    public const N_COLLECTOR  = 'collector';
    public const N_DEBUG      = 'debug';

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
    protected static string $storage_path = IRM_WPNB_LOGS_PATH;

    /**
     * @since   0.9.0
     * @var     string
     */
    protected static string $extension   = '.log';

    /**
     * @since   0.9.0
     * @var     string
     */
    protected static string $structure = '[%s] (%s) %s %s';

    /**
     * get log file path
     *
     * @since   0.9.0
     * @param   string  $name
     * @return  string
     */
    public static function get_path(string $name): string
    {
        return Data::join_path([self::$storage_path, $name . self::$extension]);
    }

    /**
     * check if storage folder created
     *
     * @since   0.9.0
     * @return  bool
     */
    protected static function have_storage(): bool
    {
        return Fs::dir_exists(self::$storage_path);
    }

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
        Logger::add('logger preload started.');

        // create storage folder for first time
        if (!Fs::dir_exists(self::$storage_path)) {
            $creating_storage = Fs::mkdir(self::$storage_path);

            Logger::add("trying to create logger storage path.", Logger::N_MAIN, Logger::LEVEL_LOG, [
                'dir'       => self::$storage_path,
                'result'    => $creating_storage
            ]);

            if (!$creating_storage) {
                Logger::add('could not create logger storage folder.', Logger::N_MAIN, Logger::LEVEL_ERROR);
            }
        }

        Logger::add('logger preload ended.');
    }

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
        $date = Date::by_format('Y-m-d H:i:s.u');

        return sprintf(self::$structure, $date, $level, $msg, self::encode_data($data));
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

        if (self::have_storage()) {
            return Fs::append($path, $text . PHP_EOL);
        }

        return false;
    }
}
