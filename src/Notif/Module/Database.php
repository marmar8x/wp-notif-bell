<?php

namespace Irmmr\WpNotifBell\Notif\Module;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Database [helper]
 * database handler
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Module
 */
class Database
{
    /**
     * get results from database
     * 
     * @since   0.9.0
     * @param   string  $query      The string mysql query
     * @param   array   $prepares   The prepare values
     * @return  array   database results
     */
    public function get_results(string $query, array $prepares = []): array
    {
        global $wpdb;

        if (!empty($prepares)) {
            $query = $wpdb->prepare($query, $prepares);
        }

        return $wpdb->get_results($query) ?? [];
    }

    /**
     * get vars from database
     * 
     * @since   0.9.0
     * @param   string  $query      The string mysql query
     * @param   array   $prepares   The prepare values
     * @return  string  database vars
     */
    public function get_var(string $query, array $prepares = []): string
    {
        global $wpdb;

        if (!empty($prepares)) {
            $query = $wpdb->prepare($query, $prepares);
        }

       return $wpdb->get_var($query) ?? '';
    }

    /**
     * run query with wpdb
     * 
     * @since   0.9.0
     * @param   string  $query      The string mysql query
     * @param   array   $prepares   The prepare values
     * @return  bool    result
     */
    public function run_query(string $query, array $prepares = []): bool
    {
        global $wpdb;

        if (!empty($prepares)) {
            $query = $wpdb->prepare($query, $prepares);
        }

       return $wpdb->query($query) ?? false;
    }

    /**
     * get wpdb last error
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_error(): string
    {
        global $wpdb;

        return $wpdb->last_error;
    }
}