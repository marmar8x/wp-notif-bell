<?php

namespace Irmmr\WpNotifBell\Notif\Instance;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Receiver
 * create identifiers to highlight notification
 * recipients
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Instance
 */
class Receiver
{
    /**
     * according what?
     * 
     * @since   0.9.0
     * @var     string
     */
    protected string $name;

    /**
     * according data string of `name`
     * 
     * @since   0.9.0
     * @var     string
     */
    protected string $data;

    /**
     * class constructor
     * 
     * @since   0.9.0
     * @param   string $name
     * @param   string $data
     * @return  void
     */
    public function __construct(string $name, string $data)
    {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * get name
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_name(): string
    {
        return $this->name;
    }

    /**
     * get data
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_data(): string
    {
        return $this->data;
    }

    /**
     * get according name and data
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get(): array
    {
        return [
            'name' => $this->name,
            'data' => $this->data
        ];
    }

    /**
     * get receiver json string
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_json(): string
    {
        return wp_json_encode($this->get());
    }

    /**
     * check accord validation
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function is_valid(): bool
    {
        return !empty($this->get_name());
    }
}