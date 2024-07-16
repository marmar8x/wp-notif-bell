<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Statics
 * Same as 'Container' class, defined only for admin
 * area and handle some static variables.
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
class Statics
{
    /**
     * [SendPage] Easy Access List
     * list of all easy access select items
     * 
     * @since   0.9.0
     * @var     array   $eza_list
     */
    public static array $eza_list = [];

    /**
     * [SendPage] Receivers name list
     * list of all receivers list
     * 
     * @since   0.9.0
     * @var     array   $rec_list
     */
    public static array $rec_list = [];
}