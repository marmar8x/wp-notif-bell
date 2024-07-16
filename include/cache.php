<?php
/**
 * cache file
 * define some cache functions
 * 
 * @since   0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Cache;

// static classes
$_wpnb_cache = new Cache;

function wpnb_cache(): Cache
{
    return new Cache;
}