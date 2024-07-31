<?php
/**
 * Uninstall file
 * Wordpress uninstaller for wpnb plugin!
 * 
 * @since   0.9.0
 */

// exit if accessed directly
defined('WP_UNINSTALL_PLUGIN') || die;

// require wpnb config file
$config_file = require plugin_dir_path(__FILE__) . 'wpnb-config.php';

use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\Logger;
use Irmmr\WpNotifBell\Settings;

global $wpdb;

// add log for uninstalling
Logger::add('Uninstalling wpnb');

// check data delete status
$delete_data = Settings::get('admin.manage.rm_data') === 'yes';

// remove all data
if ($delete_data) {
    // add log for removing data
    Logger::add('Deletion of data is active. Deleting all plugin data');

	// delete all user meta
	$wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE 'wpnb\_%';");

    // delete database tables
    foreach (Db::TABLES_NAME as $table => $name) {
        $table_name = Db::get_name($name, $wpdb->prefix);

        $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
    }

    // remove all options
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'wpnb\_%'");

    // remove main storage folder
    // The storage folder must be removed manually
    // path  =>   /wp-content/wpnb

    // add log for after delete data
    Logger::add('The data of the plugin has been deleted');
}

// add log for uninstalling
Logger::add('wpnb uninstalled');