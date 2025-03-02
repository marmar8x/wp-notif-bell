<?php

namespace Irmmr\WpNotifBell\Interfaces;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Interface ApiInterface
 * all api interfaces
 *
 * @since    1.0.0
 * @package  Irmmr\WpNotifBell\Interfaces
 */
interface ApiInterface
{
    // api slug
    // @since   1.0.0
    public const SLUG = 'wpnb';

    // api version
    // @since   1.0.0
    public const VER_1 = self::SLUG . '/v1';

    // db columns
    // @since   1.0.0
    public const DB_COLUMNS = ['id', 'key', 'sender', 'title', 'tags', 'content', 'format', 'recipients', 'data', 'sent_at', 'created_at', 'updated_at'];
}