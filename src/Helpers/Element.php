<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Element
 * Make html element and manage them
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Helpers
 */
class Element
{
    // self closing elements
    // @since   0.9.0
    private const SELF_CLOSINGS = [
        'area',
        'base',
        'br',
        'col',
        'hr',
        'embed',
        'img',
        'input',
        'meta',
        'link',
        'param',
        'source',
        'track',
        'wbr'
    ];

    /**
     * is it self closing? ></ | />
     * 
     * @since   0.9.0
     * @param   string  $type
     * @return  bool
     */
    public static function is_self_closing(string $type): bool
    {
        return in_array($type, self::SELF_CLOSINGS);
    }

    /**
     * create html elements
     * 
     * @since   0.9.0
     * @param   string  $type
     * @param   string  $value
     * @param   array   $args
     * @return  string
     */
    public static function create(string $type, string $value, array $args = []): string
    {
        $self_closing = self::is_self_closing($type);

        $end_element = $self_closing ? ' />' : '>';
        $arg_element = '';

        $args_fetch = [];
        foreach ($args as $n => $v) {
            if (substr($n, 0, 1) !== '_') {
                $args_fetch[] = $n . '="' . esc_attr($v) . '"';
            }
        }

        $arg_element = implode(' ', $args_fetch);
        $arg_element = empty($arg_element) ? '' : ' ' . $arg_element;

        $_start = "<{$type}{$arg_element}{$end_element}";
        $_value = $value;
        $_end   = !$self_closing ? "</{$type}>" : '';

        return $_start . $_value . $_end;
    }
}
