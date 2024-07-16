<?php
/**
 * Send file
 * define settings for send page
 * 
 * @since   0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Admin\Statics;
use Irmmr\WpNotifBell\Helpers\Element;

/**
 * [Admin]
 * render easy access option list
 * 
 * @since   0.9.0
 * @return  void
 */
function wpnb_render_eza_list(): void
{
    $list = Statics::$eza_list;

    foreach ($list as $id => $data) {
        if (!is_string($id) || !is_array($data)) {
            continue;
        }

        $args = [
            'data-wpnb-ez' => $data['data'] ?? '',
            ...$data['args'] ?? []
        ];

        echo Element::create('option', $data['value'] ?? '', $args);
    }
}

/**
 * [Admin]
 * render receivers list option list
 * 
 * @since   0.9.0
 * @return  void
 */
function wpnb_render_rec_list(): void
{
    $list = Statics::$rec_list;

    foreach ($list as $value => $data) {
        if (!is_string($value) || !is_array($data)) {
            continue;
        }

        $data['args'] = $data['args'] ?? [];
        $data['args']['value'] = $value;

        $args = [
            'data-wpnb-text' => $data['text'] ?? '',
            ...$data['args']
        ];

        echo Element::create('option', $data['title'] ?? '', $args);
    }
}
