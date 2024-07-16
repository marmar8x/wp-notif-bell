<?php
/**
 * Settings file
 * init settings function and variables
 * 
 * @since   0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Settings;

/**
 * all settings
 * !! do not overwrite this global variable to save new settings. for saving
 * new settings you must use `Settings::save()` or `wpnb_save_settings` func.
 * 
 * @since   0.9.0
 * @global  $wpnb_settings
 */
$GLOBALS['wpnb_settings'] = Settings::get_all();

/**
 * get settings
 * 
 * @since   0.9.0
 * @return  array
 */
function wpnb_get_settings(): array
{
    global $wpnb_settings;

    if (!isset($wpnb_settings)) {
        $wpnb_settings = Settings::get_all();
    }

    return $wpnb_settings;
}

/**
 * save settings
 * 
 * @since   0.9.0
 * @param   array   $settings
 * @return  void
 */
function wpnb_save_settings(array $settings): void
{
    Settings::save($settings);
}

/**
 * get setting from current settings
 * ! [intermediary] once the settings are received from the database and
 * then received from the previous data.
 * 
 * @since   0.9.0
 * @param   string   $settings
 * @return  void
 */
function wpnb_get_setting(string $name, $default = '')
{
    return wpnb_get_settings()[$name] ?? $default;
}
