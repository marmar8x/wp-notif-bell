<?php

namespace Irmmr\WpNotifBell\Notif;

// If this file is called directly, abort.
defined('WPINC') || die;

use stdClass;

/**
 * Class Notification
 * a class for every notification with all
 * notif data
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif
 */
class Notification
{
    /**
     * @since   0.9.0
     * @var     string
     */
    public string $sender;

    /**
     * @since   0.9.0
     * @var     string
     */
    public string $title;

    /**
     * @since   0.9.0
     * @var     string
     */
    public string $content;

    /**
     * @since   0.9.0
     * @var     string
     */
    public string $send_date;

    /**
     * @since   0.9.0
     * @var     string
     */
    public string $update_date;

    /**
     * @since   0.9.0
     * @var     array<Receiver>
     */
    public array $receivers = [];

    /**
     * @since   0.9.0
     * @var     array
     */
    public array $recipients = [];

    /**
     * @since   0.9.0
     * @var     string
     */
    public string $data_token;

    /**
     * database data
     * 
     * @since   0.9.0
     * @var     stdClass
     */
    public stdClass $data;

    /**
     * class constructor
     * init notification data after database select
     * 
     * @param   stdClass    $data
     * @since   0.9.0
     */
    public function __construct(stdClass $data)
    {
        $this->data = $data;

        $this->sender       = $data->sender;
        $this->title        = $data->title;
        $this->content      = $data->content;
        $this->send_date    = $data->created_at;
        $this->update_date  = $data->updated_at;
        $this->data_token   = $data->dtk;

        $this->recipients   = json_decode($data->recipients, true);

        foreach ($this->recipients as $recipient) {
            $this->receivers[] = new Receiver($recipient['name'], $recipient['data']);
        }
    }
}