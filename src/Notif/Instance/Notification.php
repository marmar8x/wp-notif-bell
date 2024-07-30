<?php

namespace Irmmr\WpNotifBell\Notif\Instance;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Module\TextMagic;
use Irmmr\WpNotifBell\Notif\Assist\Data;
use Irmmr\WpNotifBell\Notif\Assist\Tags;
use Irmmr\WpNotifBell\Notif\Instance\Data as InstanceData;
use Parsedown;
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
    public string $format;

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
     * registered textmagic
     * 
     * @since   0.9.0
     * @param   TextMagic   $text_magic
     */
    protected TextMagic $text_magic;

    /**
     * textmagic instance
     * 
     * @since   0.9.0
     * @return  TextMagic
     */
    public function textmagic(): TextMagic
    {
        return $this->text_magic;
    }

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
        // create a textmagic module for this notif
        $this->text_magic = new TextMagic;

        // get main data for initialize notif class
        $this->configs      = $configs;
        $this->instances    = $instances;
        $this->fetch        = $fetch;

        // without any data, abort
        if (empty($fetch)) {
            return;
        }

        // get the text format
        $this->format       = $fetch->format ?? 'pure-text';

        // get every notif data from db fetch
        $this->key          = $fetch->key ?? '';
        $this->sender       = $fetch->sender ?? '';
        $this->title        = $fetch->title ?? '';
        $this->content      = $fetch->content ?? '';
        $this->send_date    = $fetch->created_at ?? '';
        $this->update_date  = $fetch->updated_at ?? '';
        $this->create_date  = $fetch->created_at ?? '';
        $this->tags         = Tags::parse($fetch->tags ?? '');

        // data parse into Data Instance
        $data_fetch = Data::parse($fetch->data);

        foreach ($data_fetch as $key => $value) {
            $this->data[] = new InstanceData($key, $value);
        }

        // get list of receivers as json
        $recipients = $fetch->recipients ?? '';

        // convert json to a list of Receivers
        if (!empty($recipients)) {
            $this->recipients   = json_decode($fetch->recipients ?? '', true);

            foreach ($this->recipients as $recipient) {
                $this->receivers[] = new Receiver($recipient['name'], $recipient['data']);
            }
        }

        // register notif data to textmagic
        $data_vars = [];

        // extract data list for notif data
        foreach ($this->data as $d) {
            $data_vars[ $d->get_key() ] = $d->get_value();
        }

        // add data vars
        $this->text_magic->set_data($data_vars);
    
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
        // render markdown data
        if ($this->format === 'markdown') {
            $parsedown = new Parsedown;

            $this->content = $parsedown->text($this->content);
        }
    }

    /**
     * get content after render
     * $this->content           =>  pure
     * $this->get_content()     =>  rendered
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_content(): string
    {
        if ($this->configs['use_textmagic']) {
            return $this->text_magic->render($this->content);
        }

        return $this->content;
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