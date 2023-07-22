<?php
/**
 * WP Notif Bell
 * @package           Irmmr\WPNotifBell
 *
 * @wordpress-plugin
 * Plugin Name:       Notif Bell
 * Plugin URI:        #
 * Description:       This plugin contains some features to create a notification bell for theme and site.
 * Version:           0.9.0
 * Author:            Irmmr
 * Author URI:        https://t.me/irmmr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-notif-bell
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined('WPINC') || die;

// require main config file
$wpnb_config_file = require plugin_dir_path(__FILE__) . 'wpnb-config.php';

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
function _run_irmmr_wp_notif_bell(): void
{
    $processor = new \Irmmr\WpNotifBell\Processor;

    $processor->init();
    $processor->run();
}

add_action('plugins_loaded', '_run_irmmr_wp_notif_bell');
