<?php

namespace Irmmr\WpNotifBell;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Option;
use Irmmr\WpNotifBell\Interfaces\SettingInterface;

/**
 * Class Settings
 * used to save/read plugin settings
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class Settings implements SettingInterface
{
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
                '_value'    => __('Enable', 'notif-bell')
            ],
            [
                'value'     => 'disable',
                '_value'    => __('Disable', 'notif-bell')
            ],
        ];

        // ready options for yes or no
        $options_yesno = [
            [
                'value'     => 'yes',
                '_value'    => __('Yes', 'notif-bell')
            ],
            [
                'value'     => 'no',
                '_value'    => __('No', 'notif-bell')
            ],
        ];

        // add settings section
        $settings['load'] = [];

        // add child tab setting lightmode
        $settings['load']['lightmode'] = [
            [
                '_title'        => __('Enable LightMode', 'notif-bell'),
                '_text'         => __('Running light mode can reduce the process of the administrator side. Without enabling lightmode, no setting will work.', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'status',
                'required'      => 'true',
                '_options'      => $options_enable
            ],
            [
                '_title'        => __('Hide Notices', 'notif-bell'),
                '_text'         => __('Hiding notices helps the system not do any processing to display notices.', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'hide_notices',
                'required'      => 'true',
                '_options'      => $options_enable
            ],
        ];

        // add debug settings
        $settings['load']['debug'] = [
            [
                '_title'        => __('Debug level', 'notif-bell'),
                '_text'         => __('You can select the plugin debug level. Active or Disable or syncing with wordpress debug?', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'level',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Disable', 'notif-bell'),
                        'value'     => 'disable'
                    ],
                    [
                        '_value'    => __('Active', 'notif-bell'),
                        'value'     => 'active'
                    ],
                    [
                        '_value'    => __('Sync', 'notif-bell'),
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
                '_title'        => __('Text editor', 'notif-bell'),
                '_text'         => __('Select the text editor that plugin must use in send page and other.', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'text_editor',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Audo detect (by format)', 'notif-bell'),
                        'value'     => 'auto'
                    ],
                    [
                        '_value'    => __('Simple textarea', 'notif-bell'),
                        'value'     => 'simple'
                    ],
                ]
            ],
            [
                '_title'        => __('Visual text editor', 'notif-bell'),
                '_text'         => __('Which visual text editor do you want to use to edit html?', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'visual_text_editor',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Wordpress editor', 'notif-bell'),
                        'value'     => 'wp'
                    ],
                    [
                        '_value'    => __('Quill editor', 'notif-bell'),
                        'value'     => 'quill'
                    ],
                ]
            ],
            [
                '_title'        => __('Editor base', 'notif-bell'),
                '_text'         => __('Should the usable editor be visual or code?', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'editor_base',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Visual', 'notif-bell'),
                        'value'     => 'visual'
                    ],
                    [
                        '_value'    => __('Code', 'notif-bell'),
                        'value'     => 'code'
                    ],
                ]
            ],
            [
                '_title'        => __('Quill editor theme', 'notif-bell'),
                '_text'         => __('Select quill editor theme for ui view.', 'notif-bell') . '<br />' . '<a target="_blank" href="https://quilljs.com/docs/configuration#theme">https://quilljs.com/docs/configuration#theme</a>',
                '_type'         => 'select',
                'id'            => 'quill_theme',
                'required'      => 'true',
                '_options'      => [
                    [
                        '_value'    => __('Snow', 'notif-bell'),
                        'value'     => 'snow'
                    ],
                    [
                        '_value'    => __('Bubble', 'notif-bell'),
                        'value'     => 'bubble'
                    ],
                ]
            ],
        ];

        // add manage section to admin
        $settings['admin']['manage'] = [
            [
                '_title'        => __('Delete data with Uninstall', 'notif-bell'),
                '_text'         => __('Remove all plugin data when uninstalling the plugin on the WordPress plugins page?', 'notif-bell'),
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
                '_title'        => __('Service status', 'notif-bell'),
                '_text'         => __('Do you want the Ajax service of the plugin to be enabled?', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'status',
                'required'      => 'true',
                '_options'      => $options_enable
            ],
            [
                '_title'        => __('Ajax: Add seen list', 'notif-bell'),
                '_text'         => __('An Ajax specifically for adding notification IDs as seen for the user through an Ajax request.', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'add_seen_list',
                'required'      => 'true',
                '_options'      => $options_enable
            ],
        ];

        // add modules section
        $settings['modules'] = [];

        // add eye fields
        $settings['modules']['eye'] = [
            [
                '_title'        => __('Eye method', 'notif-bell'),
                '_text'         => __('The module method for write and read data. [recommended: auto]', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'method',
                'required'      => 'true',
                '_options'      => [
                    [
                        'value'     => 'auto',
                        '_value'    => __('Auto [select based on data size]', 'notif-bell')
                    ],
                    [
                        'value'     => 'bin',
                        '_value'    => __('Binary [large]', 'notif-bell')
                    ],
                    [
                        'value'     => 'comma',
                        '_value'    => __('Comma separated [small]', 'notif-bell')
                    ],
                ]
            ],
            [
                '_title'        => __('Eye manager status', 'notif-bell'),
                '_text'         => __('Manager can modify and change data types and methods based on entry data for "AUTO" method. [recommended: enable]', 'notif-bell'),
                '_type'         => 'select',
                'id'            => 'manager',
                'required'      => 'true',
                '_options'      => $options_enable
            ],
            [
                '_title'        => __('Eye count limit', 'notif-bell'),
                '_text'         => __('After this count of notifications ids that inserted in "seen" list, module will automatically convert list ot binary.', 'notif-bell'),
                '_type'         => 'input',
                'type'          => 'number',
                'id'            => 'count_limit',
                'max'           => 300,
                'min'           => 30,
                'required'      => 'true'
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