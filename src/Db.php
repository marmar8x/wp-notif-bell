<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Option;

/**
 * Class Db
 * database manages for plugin starts with
 * this class
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class Db
{
    // acronym of notification-bell
    // @since 0.9.0
    public const PREFIX = 'nb_';

    // list of all table names
    // @since 0.9.0
    public const TABLES_NAME = [
        // include all notifications
        // @since 0.9.0
        'notifs' => 'notifs'
    ];

    // latest version of database
    // @since 0.9.0
    public const LATEST_VERSION = '1.0';

    /**
     * database tables
     * @since   0.9.0
     * @var     array
     */
    private static array $tables = [
        [
            // notif table: list of all notifications
            'name'  => self::TABLES_NAME['notifs'],
            'since' => '0.9.0',
            'query' => "CREATE TABLE IF NOT EXISTS `%n:notifs%` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'notif ID',
                `key` VARCHAR(200) NOT NULL COMMENT 'notif key',
                `sender` VARCHAR(100) NOT NULL COMMENT 'notif sender',
                `title` TEXT NOT NULL COMMENT 'notif title',
                `tags` TEXT DEFAULT NULL COMMENT 'notif tags',
                `content` LONGTEXT NOT NULL COMMENT 'notif content',
                `format` VARCHAR(100) NOT NULL COMMENT 'notif content format',
                `recipients` JSON NOT NULL COMMENT 'notif audiences',
                `data` TEXT DEFAULT NULL COMMENT 'notif data',
                `sent_at` datetime NOT NULL COMMENT 'notif send date',
                `created_at` datetime NOT NULL COMMENT 'notif creation date',
                `updated_at` datetime NOT NULL COMMENT 'notif modification date',
                PRIMARY KEY(`id`)
            ) %charset_collate%;"
        ]
    ];

    /**
     * database table updates
     * @since   0.9.0
     * @var     array
     */
    private static array $updates = [
        [
            'version'   => ['0.8.0', '=='],
            'query'     => [
                "DROP TABLE `%n:notifs%` IF EXISTS;"
            ]
        ]
    ];

    /**
     * render query string with all parameters
     * 
     * @since   0.9.0
     * @param   string    $query
     * @param   array     $data
     * @return  string
     */
    private static function render_query(string $query, array $data = []): string
    {
        $charset_collate = $data['cc'] ?? '';
        $prefix          = $data['prefix'] ?? '';

        // :wp_prefix
        $query = str_replace('%wp_prefix%', $prefix, $query);

        // :wpnb_prefix
        $query = str_replace('%wpnb_prefix%', self::PREFIX, $query);

        // :prefix
        $query = str_replace('%prefix%', $prefix . self::PREFIX, $query);

        // :charset_collate
        $query = str_replace('%charset_collate%', $charset_collate, $query);

        // name search for queries
        $query = preg_replace_callback('/\%n:(?P<name>\w+)\%/is', function ($matches) use ($prefix) {
            $name = $matches['name'];

            return self::get_name($name, $prefix);
        }, $query);

        return $query;
    }

    /**
     * get a table name with prefix
     * 
     * @since   0.9.0
     * @param   string  $name
     * @param   string  $wp_prefix
     * @return  string
     */
    public static function get_name(string $name, string $wp_prefix): string
    {
        return $wp_prefix . self::PREFIX . $name;
    }

    /**
     * get a table full name
     * 
     * @since   0.9.0
     * @param   string  $name
     * @return  string
     */
    public static function table_name(string $name): string
    {
        if (!array_key_exists($name, self::TABLES_NAME)) {
            return '';
        }

        global $wpdb;

        return self::get_name(self::TABLES_NAME[$name], $wpdb->prefix);
    }

    /**
     * create all tables of this plugin
     * 
     * @since   0.9.0
     * @return  void
     */
    public static function create_tables(): void
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $prefix          = $wpdb->prefix;

        $tables     = self::$tables;
        $queries    = [];

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        foreach ($tables as $table) {
            $name       = $table['name'];

            $query      = is_string($query = $table['query']) ? [$query] : $query;
            $query      = array_map(function ($q) use ($charset_collate, $prefix) {
                return self::render_query($q, [
                    'prefix' => $prefix,
                    'cc'     => $charset_collate
                ]);
            }, $query);

            Logger::add("Building `{$name}` table query", Logger::N_MAIN, Logger::LEVEL_LOG);

            $queries[] = implode(PHP_EOL, $query);
        }

        $execute        = dbDelta($queries);

        foreach ($execute as $result) {
            Logger::add("dbDelta: {$result}", Logger::N_MAIN, Logger::LEVEL_LOG);
        }

        // check for any errors
        if ($wpdb->last_error !== '') {
            Logger::add("wpdb: {$wpdb->last_error}", Logger::N_MAIN, Logger::LEVEL_ERROR);
        }
    }

    /**
     * remove all tables of this plugin
     * 
     * @since   0.9.0
     * @return  void
     */
    public static function remove_tables(): void
    {
        global $wpdb;

        foreach (self::TABLES_NAME as $id => $name) {
            $name = self::get_name($name, $wpdb->prefix);
            $result = $wpdb->query("DROP TABLE IF EXISTS `{$name}`;");

            Logger::add("deleting `{$name}` table.", Logger::N_MAIN, Logger::LEVEL_LOG, [
                'result' => $result,
                'id'     => $id
            ]);
        }
    }

    /**
     * set database version
     * 
     * @since   0.9.0
     * @param   string $version
     * @return  bool
     */
    public static function set_version(string $version): bool
    {
        return Option::set('wpnb_db_version', $version);
    }

    /**
     * get database version
     * 
     * @since   0.9.0
     * @return  string
     */
    public static function get_version(): string
    {
        return Option::get('wpnb_db_version', '');
    }

    /**
     * check if the current database version is
     * the last one
     * 
     * @since   0.9.0
     * @return  bool
     */
    public static function is_last_version(): bool
    {
        return version_compare(self::LATEST_VERSION, self::get_version(), '==');
    }
}