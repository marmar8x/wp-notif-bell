<?php

namespace Irmmr\WpNotifBell\Notif;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\Error\Err;
use Irmmr\WpNotifBell\Error\Stack;
use Irmmr\WpNotifBell\Notif\Instance\Notification;
use Irmmr\WpNotifBell\Notif\Instance\Receiver;
use Irmmr\WpNotifBell\Notif\Module\Database;
use Irmmr\WpNotifBell\Notif\Module\Observer;
use Irmmr\WpNotifBell\Notif\Module\Pagination;
use Irmmr\WpNotifBell\Module\Query\Selector as Query;
use Irmmr\WpNotifBell\Notif\Assist\Formatter;
use Irmmr\WpNotifBell\Notif\Module\User as ModuleUser;
use Irmmr\WpNotifBell\Traits\ConfigTrait;
use Irmmr\WpNotifBell\User;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class Collector
 * collect and show all notifications
 * 
 * - database fetch:          get()
 * - notifications fetch:     fetch()
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif
 */
final class Collector
{
    use ConfigTrait;

    /**
     * table name
     * database table name
     * 
     * @since   0.9.0
     * @var     string  $table_name
     */
    protected string $table_name;

    /**
     * errors stack
     * 
     * @since   0.9.0
     * @var     Stack   $errors
     */
    protected Stack $errors;

    /**
     * pagination
     * 
     * @since   0.9.0
     * @var     Pagination   $pagination
     */
    protected Pagination $pagination;

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
     * collector constructor
     * 
     * @since   0.9.0
     */
    public function __construct()
    {
        $this->table_name   = Db::get_table_name('notifs');

        $this->errors       = new Stack;
        $this->pagination   = new Pagination($this);
        $this->db           = new Database;
        $this->query        = new Query($this->table_name);

        // default configs
        $this->configs_default = $this->configs = [
            // TextMagic: render all tags and variables for a live text
            'use_textmagic'  => true,
            'where_operator' => 'AND'
        ];
    }

    /**
     * get selector
     * 
     * @since   0.9.0
     * @return  Select
     */
    public function select(): Select
    {
        return $this->query->selector();
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
     * get database results (pure)
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get(): array
    {
        $query  = $this->query->get();
        $fetch  = (array) $this->db->get_results($query, $this->query->get_builder_values());
        $format = [];

        // decode and clean collected list
        foreach ($fetch as $notif) {
            $format[] = Formatter::decode($notif);
        }

        return $format;
    }

    /**
     * get database var
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_var(): string
    {
        $query = $this->query->get();
        $fetch = $this->db->get_var($query, $this->query->get_builder_values());

        return $fetch;
    }

    /**
     * get fetched notifications
     * 
     * @since   0.9.0
     * @return  array<Notification> 
     */
    public function fetch(): array
    {
        $fetch  = $this->get();
        $result = [];

        $instances = [];

        foreach ($fetch as $notif) {
            if (isset($notif->key)) {
                $result[] = new Notification($notif, $this->configs, $instances);
            }
        }

        return $result;
    }

    /**
     * fetch only 1 notif [the first one]
     * 
     * @since   0.9.0
     * @return  Notification|null
     */
    public function fetch_one(): ?Notification
    {
        $fetch = $this->fetch();

        return !empty($fetch) ? $fetch[0] : null;
    }

    // handlers

    /**
     * create target query for mysql json column
     * build target query for custom target using ->select()->where()
     * 
     * JSON_CONTAINS(`recipients`, \'%s\')
     * 
     * @since   0.9.0
     * @param   Receiver    $receiver
     * @return  string
     */
    public function get_target_query(Receiver $receiver): string
    {
        $json   = $receiver->get_json();
        
        return sprintf('JSON_CONTAINS(`recipients`, \'%s\')', $json);
    }

    /**
     * get where operator
     * 
     * @since   0.9.0
     * @return  string
     */
    protected function get_where_operator(): string
    {
        return $this->get_config('where_operator', 'AND');
    }

    /**
     * target with any receiver.
     * 
     * JSON_CONTAINS(`recipients`, \'%s\')
     * 
     * @since   0.9.0
     * @param   Receiver    $receiver
     * @return  self
     */
    public function target(Receiver $receiver): self
    {
        $this->select()
            ->where( $this->get_where_operator() )
                ->asLiteral( $this->get_target_query($receiver) )
                ->end();

        return $this;
    }

    /**
     * target with any receiver (multiple).
     * 
     * @since   0.9.0
     * @param   array<Receiver>  $receivers
     * @return  self
     */
    public function targets(array $receivers): self
    {
        $select = $this->select()->where( $this->get_where_operator() )
            ->subWhere('OR');

        foreach ($receivers as $receiver) {
            if ($receiver instanceof Receiver) {
                $select->asLiteral( $this->get_target_query($receiver) );
            }
        }

        $select->end();

        return $this;
    }

    /**
     * target a user for collector
     * 
     * @since   0.9.0
     * @param   int|WP_User $user
     * @return  self
     */
    public function target_by_user($user): self
    {
        $user_identity = User::get_identity($user);

        if (empty($user_identity)) {
            $this->errors->add( Err::_('user_target', 'can\'t find this user.', ['user' => $user]) );

            return $this;
        }
        
        $targets = [];

        foreach ($user_identity as $id) {
            $targets[] = new Receiver($id['name'], $id['data']);
        }

        $this->targets($targets);

        return $this;
    }

    /**
     * target multiple users for collector
     * 
     * @since   0.9.0
     * @param   array<int|WP_User> $users
     * @return  self
     */
    public function target_by_users(array $users): self
    {
        $targets = [];

        foreach ($users as $user) {
            $user_identity = User::get_identity($user);

            if (empty($user_identity)) {
                $this->errors->add( Err::_('user_target', 'can\'t find this user.', ['user' => $user]) );
    
                continue;
            }
            
            foreach ($user_identity as $id) {
                $receiver = new Receiver($id['name'], $id['data']);
                $targets[] = $this->get_target_query($receiver);
            }
        }

        // remove duplicate targets
        $targets = array_unique($targets);

        if (empty($targets)) {
            return $this;
        }

        $select = $this->select()->where( $this->get_where_operator() )
            ->subWhere('OR');

        foreach ($targets as $target) {
            $select->asLiteral($target);
        }

        $select->end();

        return $this;
    }

    /**
     * target with notif unique key
     * 
     * @since   0.9.0
     * @param   string $key
     * @return  self
     */
    public function target_by_key(string $key): self
    {
        $this->select()
            ->where( $this->get_where_operator() )
                ->equals('key', $key)
                ->end();

        return $this;
    }

    // errors

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
     * get errors list
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_errors(): array
    {
        return $this->errors->get();
    }

    // pagination

    /**
     * get pagination instance from collector
     * 
     * @since   0.9.0
     * @return  Pagination
     */
    public function pagination(): Pagination
    {
        return $this->pagination;
    }

    // observer

    /**
     * start observer over collector
     * 
     * @since   0.9.0
     * @param   \WP_User    $user
     * @return  Observer
     */
    public function observer(\WP_User $user): Observer
    {
        return new Observer($user, $this);
    }

    // user

    /**
     * start user module
     * 
     * @since   0.9.0
     * @param   \WP_User    $user
     * @return  ModuleUser
     */
    public function user(\WP_User $user): ModuleUser
    {
        return new ModuleUser($user, $this);
    }

    // count

    /**
     * get selector count without changing
     * main builder and selector
     * 
     * @since   0.9.0
     * @param   bool    $ignore_limit
     * @return  int
     */
    public function get_count(bool $ignore_limit = true): int
    {
        // create new query with current builder
        $query = new Query(
            $this->table_name,
            $this->query->builder(),
            $this->query->selector()->__clone()
        );

        // set query builder mode to `count`
        $query->selector()->count();

        // ignore limit option for result
        if ($ignore_limit) {
            $query->selector()->limit(null, null);
        }

        // get the built query
        $build_query = $query->get();

        // try to get response using database var
        $fetch = $this->db->get_var($build_query, $query->get_builder_values());

        // convert result to integer
        return intval($fetch);
    }

    // helping

    /**
     * check for any result exists
     * 
     * @since   0.9.0
     * @param   bool    $ignore_limit
     * @return  int
     */
    public function has(): bool
    {
        return $this->get_count() > 0;
    }
}