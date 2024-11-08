<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Interfaces\CapInterface;

/**
 * Class MenuBar
 * manage admin bar
 * 
 * @since    0.9.6
 * @package  Irmmr\WpNotifBell\Admin
 */
class MenuBar implements CapInterface
{
    /**
     * class constructor
     * 
     * @since    0.9.6
     */
    public function __construct()
    {
        add_action('admin_bar_menu', [$this, 'register'], 881);
    }

    /**
     * register all admin bar menus.
     * 
     * @since    0.9.6
     * @param   \WP_Admin_Bar $wp_admin_bar
     * @return   void
     */
    public function register(\WP_Admin_Bar $wp_admin_bar): void
    {
        // top `+ new` menu bars
        $this->add_new_bars($wp_admin_bar);
    }

    /**
     * [main] register all menus in admin `new` bar
     * 
     * @since    0.9.6
     * @param   \WP_Admin_Bar $wp_admin_bar
     * @return   void
     */
    private function add_new_bars(\WP_Admin_Bar $wp_admin_bar): void
    {
        $admin_url = get_admin_url();

        if (current_user_can(self::CAPS['send'])) {
            $wp_admin_bar->add_node([
                'id'        => 'wpnb_notif',
                'title'     => __('Notif', 'notif-bell'),
                'href'      => $admin_url . 'admin.php?page=wpnb-send',
                'parent'    => 'new-content'
            ]);
        }
    }
}