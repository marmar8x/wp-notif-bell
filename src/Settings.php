<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Option;

/**
 * Class Settings
 * used to save/read plugin settings
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class Settings
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
        'load.debug.level'  => 'sync'
    ];

    /**
     * [Admin]
     * get setting fields for admin settings page
     * setting field ID:    section_tab_id  
     * setting field name:  section.tab.id
     * 
     * @since   0.9.0
     * @return  array
     */
    public static function get_setting_fields(): array
    {
        $settings = [];

        // some ready options and fields
        $options_enable = [
            [
                'value'     => 'enable',
                '_value'    => __('Enable', 'wp-notif-bell')
            ],
            [
                'value'     => 'disable',
                '_value'    => __('Disable', 'wp-notif-bell')
            ],
        ];

        // add settings section
        $settings['load'] = [];

        // add child tab setting lightmode
        $settings['load']['lightmode'] = [
            [
                '_title'        => __('Enable LightMode', 'wp-notif-bell'),
                '_text'         => __('Running light mode can reduce the process of the administrator side. Without enabling lightmode, no setting will work.', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'status',
                'required'      => 'true',
                '_options'      => $options_enable
            ],
            [
                '_title'        => __('Hide Notices', 'wp-notif-bell'),
                '_text'         => __('Hiding notices helps the system not do any processing to display notices.', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'hide_notices',
                'required'      => 'true',
                '_options'      => $options_enable
            ],
        ];

        // add debug settings
        $settings['load']['debug'] = [
            [
                '_title'        => __('Debug level', 'wp-notif-bell'),
                '_text'         => __('You can select the plugin debug level. Active or Disable or syncing with wordpress debug?', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'level',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Disable', 'wp-notif-bell'),
                        'value'     => 'disable'
                    ],
                    [
                        '_value'    => __('Active', 'wp-notif-bell'),
                        'value'     => 'active'
                    ],
                    [
                        '_value'    => __('Sync', 'wp-notif-bell'),
                        'value'     => 'sync'
                    ],
                ]
            ],
        ];

       return $settings; 
    }

    /**
     * save settings
     * 
     * @since   0.9.0
     * @param   array   $settings
     * @return  bool
     */
    public static function save(array $settings): bool
    {
        $settings = array_merge(self::DEF_SETTINGS, $settings);

        // [Debug] runs only when debugging
        if (Container::$debugging) {
            Logger::add('saving settings', Logger::N_DEBUG, Logger::LEVEL_LOG, [
                'settings' => $settings
            ]);
        }

        return Option::set(self::OPTION_NAME, $settings, true);
    }

    /**
     * get all settings
     * 
     * @since   0.9.0
     * @return  array
     */
    public static function get_all(): array
    {
        return (array) Option::get(self::OPTION_NAME, self::DEF_SETTINGS);
    }

    /**
     * get settings
     * 
     * @since   0.9.0
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    public static function get(string $name, $default = '')
    {
        return self::get_all()[$name] ?? $default;
    }
}