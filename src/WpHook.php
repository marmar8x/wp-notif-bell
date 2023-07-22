<?php

namespace Irmmr\WpNotifBell;

/**
 * Class WpHook
 * activate and deactivate hooks
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class WpHook
{
    /**
     * Activate hook
     * 
     * @since   0.9.0
     * @return  void
     */
    public function activate(): void
    {
        $php_version  = PHP_VERSION;
        $required_php = IRM_WP_NOTIF_BELL_PHP;

        if (version_compare($php_version, $required_php, '<')) {
            wp_die("You need PHP version {$required_php} to use this plugin. You are currently using {$php_version}");
            return;
        }

        $this->create_db_tables();

        Logger::add('plugin activated.');
    }

    /**
     * Deactivate hook
     * 
     * @since   0.9.0
     * @return  void
     */
    public function deactivate(): void
    {
        $this->remove_db_tables();

        Logger::add('plugin deactivated.');
    }

    /**
     * create database tables
     * 
     * @since   0.9.0
     * @return  void
     */
    private function create_db_tables(): void
    {

        $exe = Db::create_tables();
        Logger::add('database tables created.', Logger::N_MAIN, Logger::LEVEL_LOG, (array) $exe);
    }

    /**
     * remove database tables
     * 
     * @since   0.9.0
     * @return  void
     */
    private function remove_db_tables(): void
    {
        Db::remove_tables();
        Logger::add('database tables removed.', Logger::N_MAIN, Logger::LEVEL_LOG);
    }
}
