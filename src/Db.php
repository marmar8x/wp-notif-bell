<?php

namespace Irmmr\WpNotifBell;

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
     * @return  array|null
     */
    public static function create_tables(): ?array
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $prefix          = $wpdb->prefix;

        $tables  = [];
        $names   = [];

        foreach (self::TABLES_NAME as $tb => $name) {
            $names[$tb] = self::get_name($name, $prefix);
        }

        // table: start     [notif table: list of all notifications]
        // @since 0.9.0
        $tables[] = "CREATE TABLE IF NOT EXISTS `{$names['notifs']}` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'notif ID',
            `key` VARCHAR(200) NOT NULL COMMENT 'notif key',
            `sender` VARCHAR(100) NOT NULL COMMENT 'notif sender',
            `title` TEXT NOT NULL COMMENT 'notif title',
            `content` LONGTEXT NOT NULL COMMENT 'notif content',
            `recipients` JSON NOT NULL COMMENT 'notif audiences',
            `created_at` datetime NOT NULL COMMENT 'notif creation date',
            `updated_at` datetime NOT NULL COMMENT 'notif modification date',
            `dtk` VARCHAR(200) NOT NULL COMMENT 'notif data token',
            PRIMARY KEY(`id`)
        ) {$charset_collate};";
        // table: end

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        return dbDelta($tables);
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
            $wpdb->query("DROP TABLE IF EXISTS `{$name}`;");
        }
    }

    /**
     * insert database notification
     * 
     * @since   0.9.0
     * @param   array $data
     * @return  bool
     */
    public static function insert_notif(array $data): bool
    {
        global $wpdb;

        $prefix     = $wpdb->prefix;
        $table_name = self::get_name(self::TABLES_NAME['notifs'], $prefix);
        $res        = $wpdb->insert($table_name, $data);

        return $res !== false;
    }
}