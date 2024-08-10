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

        // ready options for yes or no
        $options_yesno = [
            [
                'value'     => 'yes',
                '_value'    => __('Yes', 'wp-notif-bell')
            ],
            [
                'value'     => 'no',
                '_value'    => __('No', 'wp-notif-bell')
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

        // add admin section
        $settings['admin'] = [];

        // add ui settings
        $settings['admin']['ui'] = [
            [
                '_title'        => __('Text editor', 'wp-notif-bell'),
                '_text'         => __('Select the text editor that plugin must use in send page and other.', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'text_editor',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Audo detect (by format)', 'wp-notif-bell'),
                        'value'     => 'auto'
                    ],
                    [
                        '_value'    => __('Simple textarea', 'wp-notif-bell'),
                        'value'     => 'simple'
                    ],
                ]
            ],
            [
                '_title'        => __('Visual text editor', 'wp-notif-bell'),
                '_text'         => __('Which visual text editor do you want to use to edit html?', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'visual_text_editor',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Wordpress editor', 'wp-notif-bell'),
                        'value'     => 'wp'
                    ],
                    [
                        '_value'    => __('Quill editor', 'wp-notif-bell'),
                        'value'     => 'quill'
                    ],
                ]
            ],
            [
                '_title'        => __('Editor base', 'wp-notif-bell'),
                '_text'         => __('Should the usable editor be visual or code?', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'editor_base',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Visual', 'wp-notif-bell'),
                        'value'     => 'visual'
                    ],
                    [
                        '_value'    => __('Code', 'wp-notif-bell'),
                        'value'     => 'code'
                    ],
                ]
            ],
            [
                '_title'        => __('Quill editor theme', 'wp-notif-bell'),
                '_text'         => __('Select quill editor theme for ui view.', 'wp-notif-bell') . '<br />' . '<a target="_blank" href="https://quilljs.com/docs/configuration#theme">https://quilljs.com/docs/configuration#theme</a>',
                '_type'         => 'select',
                'id'            => 'quill_theme',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Snow', 'wp-notif-bell'),
                        'value'     => 'snow'
                    ],
                    [
                        '_value'    => __('Bubble', 'wp-notif-bell'),
                        'value'     => 'bubble'
                    ],
                ]
            ],
        ];

        // add manage section to admin
        $settings['admin']['manage'] = [
            [
                '_title'        => __('Delete data with Uninstall', 'wp-notif-bell'),
                '_text'         => __('Remove all plugin data when uninstalling the plugin on the WordPress plugins page?', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'rm_data',
                'required'      => 'true',
                '_options'      => $options_yesno
            ],
        ];

        // add api section
        $settings['api'] = [];

        // add ajax api fields
        $settings['api']['ajax'] = [
            [
                '_title'        => __('Service status', 'wp-notif-bell'),
                '_text'         => __('Do you want the Ajax service of the plugin to be enabled?', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'status',
                'required'      => 'true',
                '_options'      => $options_enable
            ],
            [
                '_title'        => __('Ajax: Add seen list', 'wp-notif-bell'),
                '_text'         => __('An Ajax specifically for adding notification IDs as seen for the user through an Ajax request.', 'wp-notif-bell'),
                '_type'         => 'select',
                'id'            => 'add_seen_list',
                'required'      => 'true',
                '_options'      => $options_enable
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