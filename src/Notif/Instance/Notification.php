<?php

namespace Irmmr\WpNotifBell\Notif\Instance;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Notif\Assist\Data;
use Irmmr\WpNotifBell\Notif\Assist\Tags;
use Irmmr\WpNotifBell\Notif\Instance\Data as InstanceData;
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
    public string $key;

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
     * @var     array<string>
     */
    public array $tags = [];

    /**
     * @since   0.9.0
     * @var     array<InstanceData>
     */
    public array $data = [];

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
     * @var     string
     */
    public string $create_date;

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
     * database data
     * 
     * @since   0.9.0
     * @var     stdClass
     */
    public stdClass $fetch;

    /**
     * collector configs
     * 
     * @since   0.9.0
     * @param   array   $configs
     */
    protected array $configs = [];

    /**
     * class instances
     * 
     * @since   0.9.0
     * @param   array   $instances
     */
    protected array $instances = [];

    /**
     * class constructor
     * init notification data after database select
     * 
     * @since   0.9.0
     * @param   stdClass    $data
     * @param   array       $config
     * @param   array       $instances
     */
    public function __construct(stdClass $fetch, array $configs = [], array $instances = [])
    {
        $this->configs      = $configs;
        $this->instances    = $instances;
        $this->fetch        = $fetch;

        if (empty($fetch)) {
            return;
        }

        $this->key          = $fetch->key ?? '';
        $this->sender       = $fetch->sender ?? '';
        $this->title        = $fetch->title ?? '';
        $this->content      = $fetch->content ?? '';
        $this->send_date    = $fetch->created_at ?? '';
        $this->update_date  = $fetch->updated_at ?? '';
        $this->create_date  = $fetch->created_at ?? '';
        $this->tags         = Tags::parse($fetch->tags ?? '');

        // data
        $data_fetch = Data::parse($fetch->data);

        foreach ($data_fetch as $key => $value) {
            $this->data[] = new InstanceData($key, $value);
        }

        $recipients = $fetch->recipients ?? '';

        if (!empty($recipients)) {
            $this->recipients   = json_decode($fetch->recipients ?? '', true);

            foreach ($this->recipients as $recipient) {
                $this->receivers[] = new Receiver($recipient['name'], $recipient['data']);
            }
        }
    
        // render content
        $this->render_content();
    }

    /**
     * render `content`
     * 
     * @since   0.9.0
     * @return  void
     */
    protected function render_content(): void
    {
        // TextMagic
        if ($this->configs['use_textmagic']) {
            $this->content = $this->instances['textmagic']->render($this->content);
        }
    }

    // user methods

    /**
     * check if notif includes tag
     * 
     * @since   0.9.0
     * @param   string  $tag
     * @return  bool
     */
    public function has_tag(string $tag): bool
    {
        return in_array($tag, $this->tags);
    }

    /**
     * get a data value
     * 
     * @since   0.9.0
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get_data_value(string $key, $default = '')
    {
        foreach ($this->data as $data) {
            if ($data->get_key() === $key) {
                return $data->get_value();
            }
        }

        return $default;
    }

    /**
     * its updated before?
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function is_updated(): bool
    {
        return $this->create_date !== $this->update_date;
    }
}