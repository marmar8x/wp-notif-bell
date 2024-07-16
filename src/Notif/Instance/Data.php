<?php

namespace Irmmr\WpNotifBell\Notif\Instance;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Data
 * Show a data with notif, key and value
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Instance
 */
class Data
{
    /**
     * the key
     * 
     * @since   0.9.0
     * @var     string
     */
    protected string $key;

    /**
     * data value
     * 
     * @since   0.9.0
     * @var     mixed
     */
    protected $value;

    /**
     * class constructor
     * 
     * @since   0.9.0
     * @param   string $key
     * @param   mixed  $value
     * @return  void
     */
    public function __construct(string $key, $value)
    {
        $this->key      = $key;
        $this->value    = $value;
    }

    /**
     * get key
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_key(): string
    {
        return $this->key;
    }

    /**
     * get data value
     * 
     * @since   0.9.0
     * @return  mixed
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * get according key and value
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get(): array
    {
        return [
            'key'   => $this->key,
            'value' => $this->value
        ];
    }

    /**
     * check validation
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function is_valid(): bool
    {
        return !empty($this->get_key());
    }
}