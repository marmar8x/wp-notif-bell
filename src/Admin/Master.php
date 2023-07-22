<?php

namespace Irmmr\WpNotifBell\Admin;

/**
 * Class Master
 * start anything about admin panel
 * and require all other admin classes
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
final class Master
{
    /**
     * class constructor
     * 
     * @since    0.9.0
     */
    public function __construct()
    {
        new Assets;
        new Menu;
    }
}