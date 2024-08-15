<?php

namespace Irmmr\WpNotifBell\Interfaces;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Interface DbInterface
 * database interface
 * 
 * @see      https://www.php-fig.org/bylaws/psr-naming-conventions/
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Interfaces
 */
interface DbInterface
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
}