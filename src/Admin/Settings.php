<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Admin\Utils\Notice;
use Irmmr\WpNotifBell\Settings as CoreSettings;

/**
 * Class Settings
 * the main admin settings management
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
class Settings
{
    // setting prefix
    // @since 0.9.0
    public const PREFIX = 'wpnb';

    /**
     * setting fields all sections
     * 
     * @var     array
     * @since   0.9.0
     */
    public array $fields = [];

    /**
     * each field txt (transaltion)
     * 
     * @var     array
     * @since   0.9.0
     */
    private array $txt = [];

    /**
     * init titles
     * 
     * @since   0.9.0
     * @return  void
     */
    private function init_txt(): void
    {
        $this->txt = [
            'sec_load'       => __('Load', 'notif-bell'),
            'tab_lightmode'  => __('Light Mode', 'notif-bell'),
            'tab_debug'      => __('Debug', 'notif-bell'),
            'sec_admin'      => __('Admin', 'notif-bell'),
            'tab_ui'         => __('UI', 'notif-bell'),
            'tab_manage'     => __('Manage', 'notif-bell'),
            'sec_api'        => __('Api', 'notif-bell'),
            'tab_ajax'       => __('Ajax', 'notif-bell'),
        ];
    }

    /**
     * get txt (title, text, ...)
     * 
     * @since   0.9.0
     * @param   string  $id     field id
     * @return  string
     */
    private function get_txt(string $id): string
    {
        return $this->txt[$id] ?? '';
    }

    /**
     * settings start
     * 
     * @since   0.9.0
     * @return  void
     */
    public function init(): void
    {
        // first of all, init titles
        $this->init_txt();

        // get fields from settings core
        $this->fields = CoreSettings::get_setting_fields();

        // 2. add all sections with fields
        $this->init_sections();

        // 3. register all this shit
        $this->register();
    }

    /**
     * class constructor
     * 
     * @since    0.9.0
     */
    public function __construct()
    {
        // register main settings fields
        add_action('admin_init', [$this, 'init']);

        // save settings
        add_action('admin_init', [$this, 'save_settings']);
    }

    /**
     * save settings with $_POST
     * 
     * @since   0.9.0
     * @return  void
     */
    public function save_settings(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['section'])) {
            return;
        }

        $section        = sanitize_key($_POST['section']);
        $section_slug   = $section . '_';
        $settings_def   = CoreSettings::DEF_SETTINGS;

        $fetch = [];

        foreach ($_POST as $id => $value) {
            // check if it belongs to section settings
            if (substr($id, 0, strlen($section_slug)) !== $section_slug) {
                continue;
            }

            // sanitize data after checking
            $id     = sanitize_key($id);
            $value  = sanitize_text_field($value);

            // get setting real id
            $real_id = preg_replace('/_/', '.', $id, 2);

            // check if it exists in default settings
            if (!array_key_exists($real_id, $settings_def)) {
                continue;
            }

            $fetch[$real_id] = $value;
        }

        $save = CoreSettings::save($fetch);

        if ($save) {
            $notice = Notice::render( __('Settings saved.', 'notif-bell'), Notice::SUCCESS );
        } else {
            $notice = Notice::render( __('No changes were saved.', 'notif-bell'), Notice::INFO );
        }

        wpnb_add_settings_msg($notice);
    }

    /**
     * register settings (final)
     * 
     * @since   0.9.0
     * @return  void
     */
    public function register(): void
    {
        // general settings
        register_setting(
            self::PREFIX . '_settings',
            self::PREFIX . '_settings',
            [$this, 'register_validate']
        );
    }

    /**
     * @since   0.9.0
     * @param   array   $args
     * @return  array   
     */
    public function register_validate($args)
    {
        if (!isset($args['_wpnb_settings'])) {
            add_settings_error(
                self::PREFIX . '_setting_notice',
                'invalid_token',
                __('The wpnb token is invalid.', 'notif-bell')
            );
        }

        return $args;
    }

    /**
     * [settings sections] -> General
     * - add whole section
     * - add fields
     * 
     * @since   0.9.0
     * @return  void
     */
    public function init_sections(): void
    {
        // default field data
        $data = [
            'class' => 'wpnb-inp-field wpnb-w-100 wpnb-mx-width-unset',
        ];

        // add all fields of setting section
        foreach ($this->fields as $section => $tabs) {
            // add a setting section :1
            wpnb_add_settings_section(
                $section,
                $this->get_txt("sec_{$section}")
            );

            foreach ($tabs as $tab => $fields) {
                // add a setting tab :1
                wpnb_add_settings_tab(
                    $tab,
                    $this->get_txt("tab_{$tab}"),
                    $section
                );

                foreach ($fields as $field) {
                    $field_id = $field['id'] ?? '';

                    $id      = "{$section}_{$tab}_" . $field_id;
                    $real_id = "{$section}.{$tab}." . $field_id;

                    $field['name'] = $id;
                    $field['id']   = $id;

                    // add setting fields :1
                    wpnb_add_settings_field(
                        $id,
                        $field['_title'] ?? '',
                        $field['_text'] ?? '',
                        $section,
                        $tab,
                        array_merge(
                            ['data-wpnb-real-id' => $real_id],
                            array_merge($field, $data)
                        )
                    );
                }
            }
        }
    }

    public function display_general(array $args): void
    {
        // nothing to do with it!
    }
}