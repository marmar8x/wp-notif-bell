<?php

namespace Irmmr\WpNotifBell\Notif;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\Helpers\Data;
use Irmmr\WpNotifBell\Helpers\Date;

/**
 * Class Sender
 * implement notification sender
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif
 */
class Sender
{
    // @since 0.9.0
    public const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * the notification sender (default: unknown)
     * 
     * @since   0.9.0
     * @var     string
     */
    private string $sender = 'unknown';

    /**
     * the title of notification
     * 
     * @since   0.9.0
     * @var     string
     */
    private string $title;

    /**
     * the text of notification (content)
     * 
     * @since   0.9.0
     * @var     string
     */
    private string $content;

    /**
     * list of notif receivers
     * 
     * @since   0.9.0
     * @var     array<Receiver>
     */
    private array $receivers = [];

    /**
     * set notif sender
     * 
     * @since   0.9.0
     * @param   string $sender
     * @return  self
     */
    public function set_sender(string $sender): self
    {
        $this->sender = Data::to_slug($sender);

        return $this;
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
        $this->title = Data::clean_db($title);

        return $this;
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
        $this->content = Data::clean_db($content);

        return $this;
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
     * check if entry data is valid to send
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function is_valid(): bool
    {
        return !empty($this->get_title()) && !empty($this->get_content())
            && !empty($this->get_receivers());
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
    private function create_key(): string
    {
        $date   = Date::by_format('Y-m-d H:i:s.u');
        $title  = $this->get_title();
        $micro  = microtime();
        $code   = Data::random_str();

        return hash('sha256', "d:{$date};t:{$title};m:{$micro};c:{$code};");
    }

    /**
     * send notif
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function send(): bool
    {
        if (!$this->is_valid()) {
            return false;
        }

        $date = Date::by_format(self::DATE_FORMAT);

        $data = [
            'key'        => $this->create_key(),
            'sender'     => $this->get_sender(),
            'title'      => $this->get_title(),
            'content'    => $this->get_content(),
            'recipients' => $this->get_recipients(),
            'created_at' => $date,
            'updated_at' => $date
        ];

        $data['dtk'] = Token::create($data);

        /**
         * action: before sending a notification and saving it in db
         * 
         * @since   0.9.0
         * @param   array   $data   notif database information 
         */
        do_action('wpnb_before_send', $data);

        // send notification (save in db)
        $result = Db::insert_notif($data);
        
        /**
         * action: after sending a notification and saving it in db
         * 
         * @since   0.9.0
         * @param   array   $data       notif database information 
         * @param   bool    $result     status of saving in db
         */
        do_action('wpnb_after_send', $data, $result);

        return $result;
    }
}