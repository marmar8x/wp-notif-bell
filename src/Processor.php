<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

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
     * run processor class (constructor)
     * 
     * @since    0.9.0
     * @return   void
     */
    public function run(): void
    {
        $this->admin();
    }

    /**
     * initialize plugin for start
     * - load i18n for multi-language
     * 
     * @since    0.9.0
     * @return   void
     */
    public function init(): void
    {
        I18n::load(IRM_WP_NOTIF_BELL_DIR . '/' . I18n::DIR);
    }

    /**
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