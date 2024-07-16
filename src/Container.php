<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Container
 * save some data for using in plugin
 *
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
final class Container
{
    /**
     * [TextMagic] /Helpers/TextMagic
     * variables container
     * 
     * @since   0.9.0
     * @var     array   $text_magic_vars
     */
    public static array $text_magic_vars = [];

    /**
     * text formats [pure-text, text, html, markdown, ...]
     * 
     * @since   0.9.0
     * @var     array   $text_formats
     */
    public static array $text_formats    = [];

    /**
     * debugging status on whole plugin (enabled or not)
     * 
     * @since   0.9.0
     * @var     bool   $debugging
     */
    public static bool $debugging     = false;

    /**
     * all plugin settings
     * reduced processing to receive each time
     * 
     * @since   0.9.0
     * @var     array   $settings
     */
    public static array $settings    = [];
}