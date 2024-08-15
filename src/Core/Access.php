<?php

namespace Irmmr\WpNotifBell\Core;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Interfaces\CapInterface;

/**
 * Class Access
 * manage Roles and Capabilities
 * It is recommended to use aam plugin for all these cases.
 * This plugin does not create new roles but it creates new capacities for roles.
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Core
 */
class Access implements CapInterface
{
    /**
     * add all caps for roles
     * 
     * @return  void
     * @since   0.9.0
     */
    public function add_caps(): void
    {
        $roles = self::DEFAULT_ROLES;

        foreach ($roles as $r) {
            $role = get_role($r);

            if ($role instanceof \WP_Role) {
                $role->add_cap(self::CAPS['send']);
                $role->add_cap(self::CAPS['view']); 
            }
        }
    }

    /**
     * class constructor
     * 
     * @since    0.9.0
     */
    public function __construct()
    {
        // add all caps
        add_action('init', [$this, 'add_caps']);
    }
}