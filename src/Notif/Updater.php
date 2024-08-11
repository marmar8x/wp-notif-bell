<?php

namespace Irmmr\WpNotifBell\Notif;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\Error\Err;
use Irmmr\WpNotifBell\Error\Stack;
use Irmmr\WpNotifBell\Helpers\Data;
use Irmmr\WpNotifBell\Helpers\Notif;
use Irmmr\WpNotifBell\Module\Query\Updater as Query;
use Irmmr\WpNotifBell\Notif\Assist\Data as AssistData;
use Irmmr\WpNotifBell\Notif\Assist\Formatter;
use Irmmr\WpNotifBell\Notif\Assist\Tags;
use Irmmr\WpNotifBell\Notif\Instance\Receiver;
use Irmmr\WpNotifBell\Notif\Module\Database;
use Irmmr\WpNotifBell\Traits\DateTrait;
use Irmmr\WpNotifBell\Traits\ResultTrait;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;

/**
 * Class Updater
 * update a notif with all options
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
 * dtk          hash key [token]
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif
 */
final class Updater
{
    use DateTrait, ResultTrait;

    /**
     * table name
     * database table name
     * 
     * @since   0.9.0
     * @var     string  $table_name
     */
    protected string $table_name;

    /**
     * setter data
     * 
     * @since   0.9.0
     * @var     array   $set
     */
    protected array $setter = [];

    /**
     * list of all sender errors
     * 
     * @since   0.9.0
     * @var     Stack
     */
    protected Stack $errors;

    /**
     * database
     * 
     * @since   0.9.0
     * @var     Database   $db
     */
    protected Database $db;

    /**
     * query
     * 
     * @since   0.9.0
     * @var     Query   $query
     */
    protected Query $query;

    /**
     * class constructor
     * create stack for errors
     * 
     * @since   0.9.0
     */
    public function __construct()
    {
        $this->table_name   = Db::get_table_name('notifs');

        $this->query        = new Query($this->table_name);
        $this->errors       = new Stack;
        $this->db           = new Database;
    }

    /**
     * get errors stack
     * 
     * @since   0.9.0
     * @return  Stack
     */
    public function errors(): Stack
    {
        return $this->errors;
    }

    /**
     * reset setter
     * 
     * @since   0.9.0
     * @return  self
     */
    public function reset(): self
    {
        $this->setter = [];

        return $this;
    }


    /**
     * find notif by key
     * 
     * @since   0.9.0
     * @param   string  $key
     * @return  self
     */
    public function find_by_key(string $key): self
    {
        $this->update()
            ->where()
                ->equals('key', $key)
                ->end();

        return $this;
    }

    /**
     * update sender
     * 
     * @since   0.9.0
     * @param   string $sender
     * @return  self
     */
    public function set_sender(string $sender): self
    {
        $this->setter['sender'] = Data::to_slug($sender);

        return $this;
    }

    /**
     * update title
     * 
     * @since   0.9.0
     * @param   string $title
     * @return  self
     */
    public function set_title(string $title): self
    {
        $this->setter['title'] = $title;

        return $this;
    }

    /**
     * update send date
     * 
     * @since   0.9.0
     * @param   string $date
     * @return  self
     */
    public function set_date(string $date): self
    {
        if (Data::is_datetime($date)) {
            $this->setter['sent_at'] = $date;
        }

        return $this;
    }

    /**
     * update format
     * 
     * @since   0.9.0
     * @param   string $format
     * @return  self
     */
    public function set_format(string $format): self
    {
        if (Notif::is_valid_format($format)) {
            $this->setter['format'] = $format;
        }

        return $this;
    }

    /**
     * update content
     * 
     * @since   0.9.0
     * @param   string $content
     * @return  self
     */
    public function set_content(string $content): self
    {
        $this->setter['content'] = Data::clean_db($content);

        return $this;
    }

    /**
     * update tags
     * 
     * @since   0.9.0
     * @param   array|null   $tags
     * @return  self
     */
    public function set_tags(?array $tags): self
    {
        if (is_null($tags)) {
            $this->setter['tags'] = null;

            return $this;
        }

        $this->setter['tags'] = Tags::encode($tags);

        return $this;
    }

    /**
     * update data
     * 
     * @since   0.9.0
     * @param   array|null   $data
     * @return  self
     */
    public function set_data(?array $data): self
    {
        if (is_null($data)) {
            $this->setter['data'] = null;

            return $this;
        }

        $this->setter['data'] = AssistData::encode($data);

        return $this;
    }

    /**
     * update a field
     * ! Not recommanded at all
     * ! This is better to leave date formats for themselves
     * 
     * @since   0.9.0
     * @param   string          $name
     * @param   string|null     $value
     * @return  self
     */
    public function set(string $name, ?string $value): self
    {
        if ($name === 'key') {
            $this->errors->add( Err::_('key_set', 'Can\'t update `key`! `key` is main notif identifier.') );
        
            return $this;
        }

        $this->setter[$name] = $value;

        return $this;
    }

    /**
     * remove from update set
     * 
     * @since   0.9.0
     * @return  self
     */
    public function unset(string $key): self
    {
        if (array_key_exists($key, $this->setter)) {
            unset($this->setter[$key]);
        }

        return $this;
    }

    /**
     * get updater
     * 
     * @since   0.9.0
     * @return  Update
     */
    public function update(): Update
    {
        return $this->query->updater();
    }

    /**
     * get query string
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_query(): string
    {
        return $this->query->get();
    }

    /**
     * check notif is updated
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function is_updated(): bool
    {
        return $this->result['status'] === 'updated';
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
        $fetch = [];

        foreach ($receivers as $receiver) {
            if ($receiver instanceof Receiver && $receiver->is_valid()) {
                $fetch[] = $receiver->get();
            }
        }

        $this->setter['recipients'] = json_encode($fetch);

        return $this;
    }

    /**
     * run updater
     * 
     * @since   0.9.0
     * @return  void
     */
    public function run(): void
    {
        // check if setter is empty
        if (empty($this->setter)) {
            $this->errors->add( Err::_('empty_setter', 'There is no data for update.') );
            $this->set_result('error', $this->errors->get());

            return;
        }

        // reset result if updated before
        // !! only in Update
        if ($this->is_updated()) {
            $this->reset_result();
            $this->errors->reset();
        }

        // get setter array
        $setter = $this->setter;

        // simple errors, use it faster
        $err = $this->errors;

        // check notif title
        if (isset($setter['title']) && empty($setter['title'])) {
            $err->add( Err::_('empty_title', 'Notif title is not set.') );
        }

        // check notif sender
        if (isset($setter['sender']) && empty($setter['sender'])) {
            $err->add( Err::_('empty_sender', 'Notif sender is not set.') );
        }

        // check notif format
        if (isset($setter['format']) && !Notif::is_valid_format($setter['format'])) {
            $err->add( Err::_('invalid_format', 'Notif format is invalid.') );
        }

        // check notif sent_at
        if (isset($setter['sent_at']) && !Data::is_datetime($setter['sent_at'])) {
            $err->add( Err::_('invalid_date', 'Notif custom send date is invalid.') );
        }

        // check notif content
        if (isset($setter['content']) && empty($setter['content'])) {
            $err->add( Err::_('empty_content', 'Notif content is not set.') );
        }

        // ! do not check receivers for allow updater to remove all

        // check for errors
        if ($err->has()) {
            $this->set_result('error', $err->get());

            return;
        }

        // set updated date | NotifTrait
        $setter['updated_at'] = $this->get_current_date();

        // format and clean data
        $setter = Formatter::encode($setter);

        // set values
        $this->update()
            ->setValues($setter);

        // set results
        $result = $this->db->run_query( $this->get_query(), $this->query->get_builder_values() );

        // check for wpdb error
        $wpdb_error = $this->db->get_error();

        if (!empty($wpdb_error)) {
            $this->errors->add( Err::_('wpdb_error', $wpdb_error) );
        }

        // query error
        if (!$result) {
            $this->errors->add( Err::_('run_error', 'The update process failed. This means no column has changed.') );
        }

        if ($err->has()) {
            $this->set_result('error', $err->get());
        } else {
            $this->set_result('updated');
        }
    }
}