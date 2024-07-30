<?php

namespace Irmmr\WpNotifBell\Notif;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\Error\Stack;
use Irmmr\WpNotifBell\Error\Err;
use Irmmr\WpNotifBell\Helpers\Data as DataHelper;
use Irmmr\WpNotifBell\Helpers\Notif;
use Irmmr\WpNotifBell\Notif\Assist\Tags;
use Irmmr\WpNotifBell\Notif\Assist\Data as AssistData;
use Irmmr\WpNotifBell\Notif\Assist\Formatter;
use Irmmr\WpNotifBell\Notif\Instance\Data;
use Irmmr\WpNotifBell\Notif\Instance\Receiver;
use Irmmr\WpNotifBell\Traits\DateTrait;
use Irmmr\WpNotifBell\Traits\ResultTrait;

/**
 * Class Sender
 * implement notification sender
 * 
 * key          hash key [id]
 * sender       string slug
 * title        string
 * tags         string with separator a,b,c,d
 * content      string
 * recipients   json
 * sent_at      date format
 * created_at   date format
 * updated_at   date format
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif
 */
final class Sender
{
    use DateTrait, ResultTrait;

    /**
     * the notification sender (default: unknown)
     * 
     * @since   0.9.0
     * @var     string
     */
    protected string $sender = 'unknown';

    /**
     * the notification format (default: pure-text)
     * 
     * @since   0.9.0
     * @var     string
     */
    protected string $format = 'pure-text';

    /**
     * the title of notification
     * 
     * @since   0.9.0
     * @var     string
     */
    protected string $title = '';

    /**
     * send date for custom send date
     * 
     * @since   0.9.0
     * @var     string
     */
    protected string $date = '';

    /**
     * the tags of notification in an array
     * 
     * @since   0.9.0
     * @var     array
     */
    protected array $tags = [];

    /**
     * the notification data [extra]
     * 
     * @since   0.9.0
     * @var     array
     */
    protected array $data = [];

    /**
     * the text of notification (content)
     * 
     * @since   0.9.0
     * @var     string
     */
    protected string $content = '';

    /**
     * list of notif receivers
     * 
     * @since   0.9.0
     * @var     array<Receiver>
     */
    protected array $receivers = [];

    /**
     * list of all sender errors
     * 
     * @since   0.9.0
     * @var     Stack
     */
    protected Stack $errors;

    /**
     * class constructor
     * create stack for errors
     * 
     * @since   0.9.0
     */
    public function __construct()
    {
        $this->errors = new Stack;
    }

    /**
     * check notif is sent
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function is_sent(): bool
    {
        return $this->result['status'] === 'sent';
    }

    /**
     * set notif sender
     * 
     * @since   0.9.0
     * @param   string $sender
     * @return  self
     */
    public function set_sender(string $sender): self
    {
        $this->sender = DataHelper::to_slug($sender);

        return $this;
    }

    /**
     * get sender
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_sender(): string
    {
        return $this->sender;
    }

    /**
     * set custom send date
     * 
     * @since   0.9.0
     * @param   string $date
     * @return  self
     */
    public function set_date(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * get custom date
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_date(): string
    {
        return $this->date;
    }

    /**
     * set notif content format
     * 
     * @since   0.9.0
     * @param   string  $format
     * @return  self
     */
    public function set_format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * get format
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_format(): string
    {
        return $this->format;
    }

    /**
     * set notif title
     * 
     * @since   0.9.0
     * @param   string $title
     * @return  self
     */
    public function set_title(string $title): self
    {
        $this->title = DataHelper::clean_db($title);

        return $this;
    }

    /**
     * get title
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_title(): string
    {
        return $this->title;
    }

    /**
     * set notif tags
     * 
     * @since   0.9.0
     * @param   array $tags
     * @return  self
     */
    public function set_tags(array $tags): self
    {
        $this->tags = Tags::clean($tags);

        return $this;
    }

    /**
     * add a notif tag
     * 
     * @since   0.9.0
     * @param   string $tag
     * @return  self
     */
    public function add_tag(string $tag): self
    {
        $tag = Tags::clean_mono($tag);

        if (!empty($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * get tags
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_tags(): array
    {
        return $this->tags;
    }

    /**
     * get tags as text
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_tags_txt(): string
    {
        if (empty($this->tags)) {
            return '';
        }

        return Tags::encode($this->tags);
    }

    /**
     * set notif data
     * 
     * @since   0.9.0
     * @return  self
     */
    public function set_data(array $data): self
    {
        $this->data = array_filter($data, function ($i) {
            return $i instanceof Data && $i->is_valid();
        });

        return $this;
    }

    /**
     * add notif data
     * 
     * @since   0.9.0
     * @param   Data    $data
     * @return  self
     */
    public function add_data(Data $data): self
    {
        $this->data[] = $data;

        return $this;
    }

    /**
     * get notif data
     * 
     * @since   0.9.0
     * @return  array<Data>
     */
    protected function get_data(): array
    {
        return $this->data;
    }

    /**
     * get notif data serialized
     * 
     * @since   0.9.0
     * @return  string|null
     */
    protected function get_data_txt(): ?string
    {
        return AssistData::encode($this->data);
    }

    /**
     * set notif content
     * 
     * @since   0.9.0
     * @param   string $content
     * @return  self
     */
    public function set_content(string $content): self
    {
        $this->content = DataHelper::clean_db($content);

        return $this;
    }

    /**
     * get content
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_content(): string
    {
        return $this->content;
    }

    /**
     * set notif receivers
     * 
     * @since   0.9.0
     * @param   array   $receivers
     * @return  self
     */
    public function set_receivers(array $receivers): self
    {
        foreach ($receivers as $receiver) {
            if ($receiver instanceof Receiver && $receiver->is_valid()) {
                $this->receivers[] = $receiver;
            }
        }

        return $this;
    }

    /**
     * add notif receiver
     * 
     * @since   0.9.0
     * @param   Receiver   $receiver
     * @return  self
     */
    public function add_receiver(Receiver $receiver): self
    {
        if ($receiver->is_valid()) {
            $this->receivers[] = $receiver;
        }

        return $this;
    }

    /**
     * get receivers
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_receivers(): array
    {
        return $this->receivers;
    }

    /**
     * get recipients (receivers by json)
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_recipients(): string
    {
        $fetch = [];

        foreach ($this->get_receivers() as $receiver) {
            $fetch[] = $receiver->get();
        }

        return json_encode($fetch);
    }

    /**
     * create a notif key
     * 
     * @since   0.9.0
     * @return  string
     */
    protected function create_key(): string
    {
        $date   = $this->get_date();
        $title  = $this->get_title();
        $micro  = microtime();
        $code   = DataHelper::random_str();

        return hash('sha256', "d:{$date};t:{$title};m:{$micro};c:{$code};");
    }

    /**
     * insert database notification
     * 
     * @since   0.9.0
     * @param   array $data
     * @return  bool
     */
    protected function insert_db(array $data): void
    {
        global $wpdb;

        $prefix     = $wpdb->prefix;
        $table_name = Db::get_name(Db::TABLES_NAME['notifs'], $prefix);
        $res        = $wpdb->insert($table_name, $data);
        $error      = $wpdb->last_error;

        if (!empty($error) || $res === false) {
            $this->errors->add(
                Err::_('query_error', 'An error occurred while inserting the notification in the database.', [
                    'error' => $error
                ])
            );
        }
    }

    /**
     * get notif key after send
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_key(): string
    {
        $data = $this->get_result_data();

        return $data['key'] ?? '';
    }

    /**
     * send notif
     * 
     * @since   0.9.0
     * @return  void
     */
    public function send(): void
    {
        // simple errors, use it faster
        $err = $this->errors;

        // check if notif sent before
        // Avoid repeated resubmissions
        if ($this->is_sent()) {
            $this->errors->add( Err::_('sent_before', 'This notif is sent before.') );
        }

        // notif title
        $title = $this->get_title();

        if (empty($title)) {
            $err->add( Err::_('empty_title', 'Notif title is not set.') );
        }

        // notif sender
        $sender = $this->get_sender();

        if (empty($sender)) {
            $err->add( Err::_('empty_sender', 'Notif sender is not set.') );
        }

        // notif format
        $format = $this->format;

        if (!Notif::is_valid_format($format)) {
            $err->add( Err::_('invalid_format', 'Notif format is invalid.') );
        }

        // notif send date
        $date = $this->date;

        if (!empty($date) && !DataHelper::is_datetime($date)) {
            $err->add( Err::_('invalid_date', 'Notif custom send date is invalid.') );
        }

        // notif content
        $content = $this->get_content();

        if (empty($content)) {
            $err->add( Err::_('empty_content', 'Notif content is not set.') );
        }

        // notif recipients
        $receivers  = $this->get_receivers();
        $recipients = $this->get_recipients();

        if (empty($receivers)) {
            $err->add( Err::_('no_receivers', 'Notif has no receiver.') );
        }

        if (!DataHelper::is_json($recipients)) {
            $err->add( Err::_('err_receivers', 'The data of the Notif recipient list is not json.') );
        }

        // notif tags [text]
        $tags = empty($this->get_tags_txt()) ? null : $this->get_tags_txt();
        
        // current date
        $current_date = $this->get_current_date();

        // check for errors and store them
        if ($err->has()) {
            $this->set_result('error', $this->errors->get());

            return;
        }

        // this notif unique key
        $key = $this->create_key();

        $data = [
            'key'        => $key,
            'sender'     => $sender,
            'title'      => $title,
            'tags'       => $tags,
            'content'    => $content,
            'format'     => $format,
            'recipients' => $this->get_recipients(),
            'data'       => $this->get_data_txt(),
            'sent_at'    => empty($date) ? $current_date : $date,
            'created_at' => $current_date,
            'updated_at' => $current_date
        ];

        // clean and format data
        $data = Formatter::encode($data);

        // send notification (save in db)
        $this->insert_db($data);

        // check for errors after send
        if ($err->has()) {
            $this->set_result('error', $this->errors->get());
        } else {
            $this->set_result('sent', [], ['key' => $key]);
        }
    }
}