<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Esc
 * escaping helpers for better usage in plugin
 * 
 * @since    0.9.5
 * @package  Irmmr\WpNotifBell\Helpers
 */
class Esc
{
    /**
     * create kses allowed html (single)
     * auto create arguments
     * 
     * @since   0.9.5
     * @param   string   $element
     * @param   array    $args
     * @return  array
     */
    public static function create_kses_allowed(string $element, array $args = []): array
    {
        $allowed = [ $element => [] ];

        foreach ($args as $name => $value) {
            $allowed[ $element ][ $name ] = [];
        }

        return $allowed;
    }

    /**
     * escaping notice html content
     *
     * @since   0.9.5
     * @return  array
     */
    public static function get_allowed_html_notice(): array
    {
        return [
            'div' => [ 'class' => [] ],
            'p'   => [],
            'b'   => [],
            'a'   => [ 'href' => [], 'target' => [], 'class' => [] ]
        ];
    }

    /**
     * escaping helper text
     *
     * @since   0.9.5
     * @return  array
     */
    public static function get_allowed_html_text(): array
    {
        return [
            'div'       => [ 'class' => [] ],
            'p'         => [],
            'b'         => [],
            'a'         => [ 'href' => [], 'target' => [], 'class' => [] ],
            'strong'    => [],
            'code'      => [],
            'pre'       => []
        ];
    }

    /**
     * escaping notif html content
     *
     * @since   0.9.5
     * @return  array
     */
    public static function get_allowed_html_content(): array
    {
        $def_attr = [ 'title' => [], 'class' => [], 'style' => [], 'id' => [] ];
        $fetch   = [];

        $allowed = [
            'h1'       => [],
            'h2'       => [],
            'h3'       => [],
            'h4'       => [],
            'h5'       => [],
            'h6'       => [],
            'ul'       => [],
            'li'       => [],
            'ol'       => [],
            'div'       => [],
            'p'         => [],
            'b'         => [],
            'a'         => [ 'href' => [], 'target' => [] ],
            'strong'    => [],
            'code'      => [],
            'span'      => [],
            'samp'      => [],
            'pre'       => [],
            'br'        => [],
            'em'        => [],
            's'         => [],
            'blockquote' => [],
            'label'     => [ 'for' => [] ],
            'img'       => [
                'alt' => [],
                'height' => [],
                'src' => [],
                'width' => []
            ],
            'path' => [ 'd' => [], 'fill' => [] ],
            'svg'  => [
                'xmlns'       => [],
                'fill'        => [],
                'viewbox'     => [],
                'role'        => [],
                'aria-hidden' => [],
                'focusable'   => [],
                'height'      => [],
                'width'       => []
            ],
        ];

        foreach ($allowed as $e => $attr) {
            $fetch[ $e ] = array_merge($def_attr, $attr);
        }

        return $fetch;
    }
}