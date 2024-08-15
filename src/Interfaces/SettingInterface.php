<?php

namespace Irmmr\WpNotifBell\Interfaces;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Interface SettingInterface
 * settings constants
 * 
 * @see      https://www.php-fig.org/bylaws/psr-naming-conventions/
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Interfaces
 */
interface SettingInterface
{
    // settings option name
    // @since 0.9.0
    public const OPTION_NAME = 'wpnb_settings';

    // default settings
    // @since 0.9.0
    public const DEF_SETTINGS = [
        // Light Mode
        'load.lightmode.status'         => 'disable',
        'load.lightmode.hide_notices'   => 'enable',
        // Debug
        'load.debug.level'              => 'sync',
        // Admin
        'admin.ui.text_editor'          => 'auto',
        'admin.ui.visual_text_editor'   => 'wp',
        'admin.ui.editor_base'          => 'visual',
        'admin.ui.quill_theme'          => 'snow',
        'admin.manage.rm_data'          => 'yes',
        // Api
        'api.ajax.status'               => 'enable',
        'api.ajax.add_seen_list'        => 'enable'
    ];
}