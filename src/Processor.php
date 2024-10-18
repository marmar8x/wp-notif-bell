<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Admin\Master;
use Irmmr\WpNotifBell\Core\Access;
use Irmmr\WpNotifBell\Core\Api;
use Irmmr\WpNotifBell\Core\Assign;
use Irmmr\WpNotifBell\Core\Notif;

/**
 * Class Processor
 * includes all main functions to start plugin
 * working
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
final class Processor
{
    /**
     * run core classes + actions
     * 
     * @since    0.9.0
     * @return   void
     */
    public function core(): void
    {
        new Assign;
        new Access;
        new Notif;
        new Api;
    }

    /**
     * run processor class (constructor)
     * 
     * @since    0.9.0
     * @return   void
     */
    public function run(): void
    {
        $this->core();
        
        $this->admin();
    }

    /**
     * initialize plugin for start
     * - load i18n for multi-language
     * - cache manager
     * 
     * @since    0.9.0
     * @return   void
     */
    public function init(): void
    {
        I18n::load(MM8X_WP_NOTIF_BELL_DIR . '/' . I18n::DIR);
    }

    /**
     * [admin main process]
     * admin section execute
     * 
     * @since    0.9.0
     * @return   void
     */
    private function admin(): void
    {
        new Master;
    }
}