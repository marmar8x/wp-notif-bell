<?php

namespace Irmmr\WpNotifBell\Core;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Container;
use Irmmr\WpNotifBell\Settings;

/**
 * Class Assign
 * enter some container variables that we need in the plugin itself to run,
 * or assign data to other container variables.
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Core
 */
class Assign
{
    /**
     * class constructor
     * 
     * @since   0.9.0
     */
    public function __construct()
    {
        $this->settings();
        $this->debugging();
    }

    /**
     * settings var
     * 
     * @since   0.9.0
     * @return  void
     */
    protected function settings(): void
    {
        Container::$settings = Settings::get_all();
    }

    /**
     * debugging var
     * 
     * @since   0.9.0
     * @return  void
     */
    protected function debugging(): void
    {
        // get debugging level from settings
        $level = Settings::get('load.debug.level', 'sync');

        if ($level === 'active') {
            $status = true;
        } elseif ($level === 'sync') {
            $status = defined('WP_DEBUG') && true === WP_DEBUG;
        } else {
            $status = false;
        }

        // set to debugging status
        Container::$debugging = $status;
    }
}