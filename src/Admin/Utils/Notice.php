<?php

namespace Irmmr\WpNotifBell\Admin\Utils;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Notice
 * render and create notices with html
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin\Utils
 */
class Notice
{
    // @since 0.9.0
    public const WARN       = 'notice-warning';
    public const ERROR      = 'notice-error';
    public const SUCCESS    = 'notice-success';
    public const INFO       = 'notice-info';

    /**
     * render notice as html
     *
     * @since   0.9.0
     * @param   string  $message
     * @param   string  $type
     * @param   bool    $dismissible
     * @return  string
     */
    public static function render(string $message, string $type = self::INFO, bool $dismissible = true): string
    {
        $classes = ['notice'];

        // add type as a class and is-dismissible class
        $classes[] = $type;

        if ($dismissible) {
            $classes[] = 'is-dismissible';
        }

        // get notif subject title
        $subjects = [
            self::WARN      => __('Warning', 'wp-notif-bell'),
            self::ERROR     => __('Error', 'wp-notif-bell')
        ];

        $subject = $subjects[ $type ] ?? '';
        $subject = !empty($subject) ? "({$subject})" : '';

        $wpnb = __('WP Notif Bell:', 'wp-notif-bell');

        // change classes to string
        $classList = implode(' ', $classes);

        return "<div class=\"{$classList}\">
            <p><b>{$wpnb}</b> {$subject} {$message}</p>
        </div>";
    }

    /**
     * print admin notice
     *
     * @since   0.9.0
     * @param   string  $message
     * @param   string  $type
     * @param   bool    $dismissible
     */
    public static function print($message, string $type = self::INFO, bool $dismissible = true): void
    {
        echo self::render($message, $type, $dismissible);
    }
}