<?php
/**
 * WP Notif Bell
 * - Less complexity along with more cache and optimal use of hardware
 * - As far as possible, the plugin has been tried to be light and without additional parts
 * - This plugin is more developer-oriented than user-oriented
 * - Use `wpnb_collector|Collector` to receive notifications and display them
 * - See `Sender` and `Collector`
 *      + Collector: Receive all notifications with the ability to target and pagination and...
 *      + Sender: Sending notifications with all tracking and tagging capabilities
 * 
 * @package           Irmmr\WPNotifBell
 *
 * @wordpress-plugin
 * Plugin Name:       Notif Bell
 * Plugin URI:        https://github.com/marmar8x/wp-notif-bell
 * Description:       This plugin implements the user notification bell for a WordPress website.
 * Version:           0.9.0
 * Author:            MarMar8x
 * Author URI:        https://github.com/marmar8x
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       notif-bell
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined('WPINC') || die;

// require main config file
$wpnb_config_file = require plugin_dir_path(__FILE__) . 'wpnb-config.php';

// check if autolaod is imported
if (is_null($wpnb_config_file['autoload'])) {
    error_log('WPNB: Failed to load `autoload`.');

    return;
}

// register an activation hook that fires when plugin acticated
register_activation_hook(__FILE__, function () {
    (new \Irmmr\WpNotifBell\WpHook)->activate();
});
 
// register an deactivation hook that fires when plugin acticated
register_deactivation_hook(__FILE__, function () {
    (new \Irmmr\WpNotifBell\WpHook)->deactivate();
});

/**
 * A function to run plugin, start all actions
 * anything will be in touch
 * 
 * @since   0.9.0
 * @private
 */
function mm8x_run_wp_notif_bell(): void
{
    $processor = new \Irmmr\WpNotifBell\Processor;

    $processor->init();
    $processor->run();
}

add_action('plugins_loaded', 'mm8x_run_wp_notif_bell');
