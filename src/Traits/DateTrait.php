<?php

namespace Irmmr\WpNotifBell\Traits;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Date;

/**
 * Trait DateTrait
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Traits
 */
trait DateTrait
{
    // @since 0.9.0
    public const DATE_FORMAT = 'Y-m-d H:i:s';
    
    /**
     * get current date by format
     * 
     * @since   0.9.0
     * @return  string
     */
    private function get_current_date(): string
    {
        return Date::by_format(self::DATE_FORMAT);
    }
}