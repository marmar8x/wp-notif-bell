<?php
/**
 * Config file
 * Define all constants and require composer autoload
 * for easier access to plugin primitives.
 * 
 * @since   0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

// if package loaded before, abort.
if (defined('WP_NOTIF_BELL')) {
    return [ 'autoload' => 'before' ];
}

// define main constants
// I add an "IRM_" as a prefix to avoid php errors due to
// same constant names!
const IRM_WP_NOTIF_BELL     = 'wp-notif-bell';
const IRM_WP_NOTIF_BELL_VER = '0.9.0';
const IRM_WP_NOTIF_BELL_PHP = '7.4';

// plugin dir path
// the plugin url path
define('IRM_WP_NOTIF_BELL_DIR', dirname( plugin_basename(__FILE__) ));
define('IRM_WP_NOTIF_BELL_PTH', plugin_dir_path(__FILE__));
define('IRM_WP_NOTIF_BELL_URL', plugin_dir_url(__FILE__));

// plugin helpers directions
const IRM_WPNB_WP_CONTENT   = ABSPATH . DIRECTORY_SEPARATOR . 'wp-content';
const IRM_WPNB_STORAGE_PATH = IRM_WPNB_WP_CONTENT . DIRECTORY_SEPARATOR . 'wpnb';
const IRM_WPNB_CACHE_PATH   = IRM_WPNB_STORAGE_PATH . DIRECTORY_SEPARATOR . 'cache';
const IRM_WPNB_LOGS_PATH    = IRM_WPNB_STORAGE_PATH . DIRECTORY_SEPARATOR . 'logs';

/**
 * Safe require for plugin
 * 
 * @since   0.9.0
 * @param   string  $path
 * @return  mixed|null
 */
function wpnb_safe_require(string ...$path)
{
    $path = implode(DIRECTORY_SEPARATOR, $path);

    if (DIRECTORY_SEPARATOR === '/') {
        $from = ['\\', '//'];
        $to   = '/';
    } else {
        $from = ['/', '\\\\'];
        $to   = '\\';
    }

    $path = str_replace($from, $to, $path);

    if (file_exists($path) && is_readable($path)) {
        return require $path;
    }

    return null;
}

// collect important data to return for requier
$_collect = [];

// import composer autoload
$_collect['autoload'] = wpnb_safe_require(IRM_WP_NOTIF_BELL_PTH, 'vendor', 'autoload.php');

// return anything main handler must know
return $_collect;