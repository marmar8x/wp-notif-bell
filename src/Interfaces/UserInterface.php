<?php

namespace Irmmr\WpNotifBell\Interfaces;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Interface UserInterface
 * user constants
 * 
 * @see      https://www.php-fig.org/bylaws/psr-naming-conventions/
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Interfaces
 */
interface UserInterface
{
    // seen list meta key name
    // @since 0.9.0
    public const SEEN_META_KEY = 'wpnb_seen_list';

    // seen list data method
    // @since 1.0.0
    public const SEEN_METHOD_KEY = 'wpnb_seen_method';

    // @since 0.9.0
    public const SEEN_SEPARATOR = ','; 
}