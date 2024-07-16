<?php

namespace Irmmr\WpNotifBell\Module;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Container;

/**
 * Class TextMagic
 * - create variables and replace them
 * 
 * ! Do not use `TextMagic` when using `Sender` directly.
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Module
 */
class TextMagic
{
    // var format in text
    // @since   0.9.0
    protected const VAR_FORMAT = '[%s]';

    /**
     * variables name and data
     * 
     * @since   0.9.0
     * @var     array<>   $vars
     */
    public array $vars = [];

    /**
     * class constructor
     * 
     * @since   0.9.0
     */
    public function __construct()
    {
        $this->vars = Container::$text_magic_vars;
    }

    /**
     * [from: old-theme]
     * register a callback for data
     *
     * @since   0.9.0
     * @param   string            $pattern    The callback/tag pattern(s).    !can change to patterns!
     * @param   string            $content    The content to be manage.
     * @param   callable          $callback   The callback to define on content.
     * @return  string
     */
    protected function register_callback(string $pattern, string $content, callable $callback): string
    {
        // check the pattern data type
        // if (!is_string($pattern) && !is_array($pattern)) {
        //     return '';
        // }

        // register callback for content
        return preg_replace_callback($pattern, $callback, $content) ?? '';
    }

    /**
     * regiser a new var or update an existing one
     * 
     * @since   0.9.0
     * @param   string              $name
     * @param   string|callable     $data
     * @return  self
     */
    public function add_var(string $name, $data): self
    {
        if (is_string($data) || is_callable($data)) {
            $this->vars[ Data::to_slug($name) ] = $data;
        }

        return $this;
    }

    /**
     * register variables with list
     * 
     * @since   0.9.0
     * @param   array   $vars
     * @return  self
     */
    public function add_vars(array $vars): self
    {
        foreach ($vars as $name => $data) {
            if (is_string($data) || is_callable($data)) {
                $this->vars[ Data::to_slug($name) ] = $data;
            }
        }

        return $this;
    }

    /**
     * check for var list exists
     * 
     * @since   0.9.0
     * @param   string   $name
     * @return  bool
     */
    public function has_var(string $name): bool
    {
        return array_key_exists($name, $this->vars);
    }

    /**
     * get a var data
     * 
     * @since   0.9.0
     * @param   string      $name
     * @param   string      $default
     * @return  bool
     */
    public function get_var(string $name, string $default = ''): string
    {
        if (!self::has_var($name)) {
            return $default;
        }

        $var = $this->vars[$name];

        if (is_string($var)) {
            return $var;
        }

        if (is_callable($var)) {
            return call_user_func($var);
        }
    }

    /**
     * [render]
     * render all variables
     * 
     * @since   0.9.0
     * @param   string  $text
     * @return  string
     */
    protected function render_vars(string $text): string
    {
        return self::register_callback('/\[([^"><]*?)\]/im', $text, function ($match) {
            $var = $match[1] ?? '';

            return self::get_var($var);
        });
    }

    /**
     * [render]
     * render options tags
     * 
     * @since   0.9.0
     * @param   string  $text
     * @return  string
     */
    protected function render_option(string $text): string
    {
        return self::register_callback('/\[opt:([^"><]*?)\]/im', $text, function ($match) {
            $name  = $match[1] ?? '';
            $value = Option::get($name, '');
            
            return strval($value);
        });
    }

    /**
     * [render]
     * render date variables
     * 
     * @since   0.9.0
     * @param   string  $text
     * @return  string
     */
    protected function render_date(string $text): string
    {
        $text = self::register_callback('/\[date:([^"><]*?)\]/im', $text, function ($match) {
            $name = $match[1] ?? '';

            if ($name === 'now') {
                $name = 'Y-m-d H:i:s';
            }

            return Date::by_format($name);
        });

        $text = self::register_callback('/\[date-i18n:([^"><]*?)\]/im', $text, function ($match) {
            $name = $match[1] ?? '';

            if ($name === 'now') {
                $name = 'Y-m-d H:i:s';
            }

            return date_i18n($name);
        });

        return $text;
    }

    /**
     * [render]
     * render user meta keys
     * get all user information
     * 
     * @since   0.9.0
     * @param   string  $text
     * @return  string
     */
    protected function render_user(string $text): string
    {
        // don't envolve with `_wp_get_current_user()` if user not logged in
        if (!is_user_logged_in()) {
            return $text;
        }

        $user = wp_get_current_user();

        if (!($user instanceof \WP_User)) {
            return $text;
        }

        // `user:key` for main user data access from WP_User
        $text = self::register_callback('/\[user:([^"><]*?)\]/im', $text, function ($match) use ($user) {
            $key    = str_replace('-', '_', $match[1] ?? '');
            $value  = $user->__isset($key) ? $user->__get($key) : '';

            return is_string($value) ? $value : '';
        });

        // `user-meta:key` for getting usermeta data
        $text = self::register_callback('/\[user-meta:([^"><]*?)\]/im', $text, function ($match) use ($user) {
            $key    = $match[1] ?? '';
            $value  = get_user_meta($user->ID, $key, true);

            return !empty($value) ? strval($value) : '';
        });

        return $text;
    }

    /**
     * remove all defined tags
     * 
     * @since   0.9.0
     * @param   string  $text
     * @return  string
     */
    protected function clean(string $text): string
    {
        return preg_replace('/\[([^"><]*?)\]/m', '', $text);
    }

    /**
     * [main method]
     * main render text
     * 
     * @since   0.9.0
     * @param   string  $text
     * @return  string
     */
    public function render(string $text): string
    {
        $text = $this->render_date($text);
        $text = $this->render_user($text);
        $text = $this->render_option($text);
        $text = $this->render_vars($text);

        return $text;
    }
}