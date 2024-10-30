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
if (defined('MM8X_WP_NOTIF_BELL')) {
    return [ 'autoload' => 'before' ];
}

// define main constants
// I add an "MM8X" as a prefix to avoid php errors due to
// same constant names!
const MM8X_WP_NOTIF_BELL     = 'wp-notif-bell';
const MM8X_WP_NOTIF_BELL_VER = '0.9.6';
const MM8X_WP_NOTIF_BELL_PHP = '7.4';
const MM8X_WP_NOTIF_BELL_DOM = 'notif-bell';

// plugin dir path
// the plugin url path
define('MM8X_WP_NOTIF_BELL_DIR', dirname( plugin_basename(__FILE__) ));
define('MM8X_WP_NOTIF_BELL_PTH', plugin_dir_path(__FILE__));
define('MM8X_WP_NOTIF_BELL_URL', plugin_dir_url(__FILE__));

// plugin helpers directions
const MM8X_WPNB_WP_CONTENT   = ABSPATH . DIRECTORY_SEPARATOR . 'wp-content';
const MM8X_WPNB_STORAGE_PATH = MM8X_WPNB_WP_CONTENT . DIRECTORY_SEPARATOR . 'wpnb';
const MM8X_WPNB_CACHE_PATH   = MM8X_WPNB_STORAGE_PATH . DIRECTORY_SEPARATOR . 'cache';
const MM8X_WPNB_LOGS_PATH    = MM8X_WPNB_STORAGE_PATH . DIRECTORY_SEPARATOR . 'logs';

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
$_collect['autoload'] = wpnb_safe_require(MM8X_WP_NOTIF_BELL_PTH, 'vendor', 'autoload.php');

// return anything main handler must know
return $_collect;