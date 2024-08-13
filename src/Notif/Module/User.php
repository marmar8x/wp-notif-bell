<?php

namespace Irmmr\WpNotifBell\Notif\Module;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Notif\Collector;

/**
 * Class User
 * handle all user actions for collector
 * a series of user helper functions to make work easier
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Module
 */
class User
{
    /**
     * collector, main notif collector
     * 
     * @since   0.9.0
     * @var     Collector   $collector
     */
    protected Collector $collector;

    /**
     * user to watch it
     * 
     * @since   0.9.0
     * @var     \WP_User    $user
     */
    protected \WP_User $user;

    /**
     * class constructor
     * 
     * @since   0.9.0
     * @param   Collector   $collector
     */
    public function __construct(\WP_User $user, Collector $collector)
    {
        $this->user      = $user;
        $this->collector = $collector;
    }

    /**
     * slice notifs by date using register date
     * ! show the notifs that sent after user register
     * 
     * @since   0.9.0
     * @param   string  $column
     * @return  self
     */
    public function register_base(string $column = 'sent_at'): self
    {
        if (!in_array($column, [ 'sent_at', 'created_at' ])) {
            $column = 'sent_at';
        }

        $registered = $this->user->user_registered;

        $this->collector
            ->select()
                ->where()
                    ->greaterThan($column, $registered)
                    ->end();
        
        return $this;
    }
}