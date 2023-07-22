<?php

namespace Irmmr\WpNotifBell;

/**
 * Class I18n
 * maintain and register multi-language feature
 * for plugin.
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell
 */
class I18n
{
    // @since 0.9.0
    public const DIR = 'languages';

    /**
     * Load text domain 
     * 
     * @since   0.9.0
     * @param   string  $domain_path
     * @return  void
     */
    public static function load(string $domain_path): void
    {
        load_plugin_textdomain(IRM_WP_NOTIF_BELL, false, $domain_path);
    }
}
