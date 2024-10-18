<?php

namespace Irmmr\WpNotifBell\Core;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Container;

/**
 * Class Notif
 * prepare some functions and filter for run
 * notif collector and sender
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Core
 */
class Notif
{
    /**
     * class constructor
     * 
     * @since   0.9.0
     */
    public function __construct()
    {
        $this->text_magic();
        $this->text_formats();
    }

    /**
     * prepare text magic functions
     * 
     * @since   0.9.0
     * @return  void
     */
    private function text_magic(): void
    {
        // get defined vars
        $vars = Container::$text_magic_vars;

        /**
         * filter of TextMagic defined variables
         * 
         * @since   0.9.0
         * @param   array   $vars   list of defined variables like:   name<string> => data<callable|string>
         */
        $vars = apply_filters('wpnb_textmagic_vars', $vars);

        // set all vars as end
        Container::$text_magic_vars = $vars;
    }

    /**
     * prepare text formats
     * 
     * @since   0.9.0
     * @return  void
     */
    private function text_formats(): void
    {
        // init formats
        $formats = Container::$text_formats = [
            'pure-text' => __('Pure Text', 'notif-bell'), // default, required
            'text'      => __('Text', 'notif-bell'),
            'html'      => __('HTML', 'notif-bell'),
            'markdown'  => __('Markdown', 'notif-bell')
        ];

        /**
         * filter of text formats
         * 
         * @since   0.9.0
         * @param   array   $formats   List of all text formats
         */
        $formats = (array) apply_filters('wpnb_text_formats', $formats);

        // 'pure-text' can't be removed
        if (!in_array('pure-text', $formats)) {
            $formats['pure-text'] = __('Pure Text', 'notif-bell');
        }

        // set all vars as end
        Container::$text_formats = $formats;
    }
}