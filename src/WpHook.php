<?php

namespace Irmmr\WpNotifBell;

use Irmmr\WpNotifBell\Helpers\Fs;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class WpHook
 * activate and deactivate hooks
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
final class WpHook
{
    //@since 0.9.0
    private const REQUIRED_FOLDERS = [
        MM8X_WPNB_STORAGE_PATH,
        MM8X_WPNB_LOGS_PATH,
        MM8X_WPNB_CACHE_PATH
    ];

    /**
     * Activate hook
     * 
     * @since   0.9.0
     * @return  void
     */
    public function activate(): void
    {
        $php_version  = PHP_VERSION;
        $required_php = MM8X_WP_NOTIF_BELL_PHP;

        // check php version
        if (version_compare($php_version, $required_php, '<')) {
            $msg = sprintf('This plugin requires at least PHP version %s to run. Your PHP version: %s', $required_php, $php_version);

            die($msg);
        }

        // before, creating required folders to prevent errors
        $this->create_folders();

        Logger::add("Starting activation process", Logger::N_MAIN, Logger::LEVEL_LOG, [
            'php-ver'       => $php_version,
            'required-php'  => $required_php
        ]);

        // create database tables
        $this->create_db_tables();

        // save latest db version
        Db::update_tables();

        // run Cache preload
        Logger::preload();

        // run Cache preload
        Cache::preload();

        // create required options
        $this->create_options();

        Logger::add('Plugin activated');
    }

    /**
     * creating required folders and path
     * 
     * @since   0.9.0
     * @return  void
     */
    private function create_folders(): void
    {
        foreach (self::REQUIRED_FOLDERS as $folder) {
            if (!Fs::dir_exists($folder)) {
                Fs::mkdir($folder);
            }
        }
    }

    /**
     * Deactivate hook
     * 
     * @since   0.9.0
     * @return  void
     */
    public function deactivate(): void
    {
        Logger::add("Starting deactivation process");

        // nothing to do

        Logger::add('Plugin deactivated');
    }

    /**
     * create required options
     * 
     * @since   0.9.0
     * @return  void
     */
    private function create_options(): void
    {
        // settings options
        // create option with default settings
        Settings::save([]);
    }

    /**
     * create database tables
     * 
     * @since   0.9.0
     * @return  void
     */
    private function create_db_tables(): void
    {
        Logger::add("Start creating database tables");

        Db::create_tables();

        Logger::add("Database tables created");
    }
}
