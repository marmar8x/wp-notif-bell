<?php

namespace Irmmr\WpNotifBell\Interfaces;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Interface CapInterface
 * all wp capabilities interfaces
 * 
 * @see      https://www.php-fig.org/bylaws/psr-naming-conventions/
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Interfaces
 */
interface CapInterface
{
    // list of all plugin caps
    // @since   0.9.0
    public const CAPS = [
        // Can send notifications using the "Send" sub-menu
        'send'   => 'wpnb_can_send',
        // Can view all notifications using "Collector" in the sub-menu
        'view'   => 'wpnb_can_view',
        // Can change settings and view plugin homepages (default admin)
        'manage' => 'manage_options'
    ];

    // list of default roles to get wpnb access
    // @since   0.9.0
    public const DEFAULT_ROLES = ['administrator'];
}