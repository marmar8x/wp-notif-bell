<?php

namespace Irmmr\WpNotifBell\Notif;

use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\User;

/**
 * Class Collector
 * implement notification sender
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif
 */
class Collector
{
    /**
     * main notifs table name wiht all prefixes
     * 
     * @since   0.9.0
     * @var     string
     */
    private string $db_table_name = '';

    /**
     * collector database query
     * 
     * @since   0.9.0
     * @var     string
     */
    private string $db_query = '';

    /**
     * user identity using `User::get_identity`
     * 
     * @since   0.9.0
     * @var     array
     */
    private array $user_identity = [];

    /**
     * db query selects using SELECT
     * 
     * @since   0.9.0
     * @var     array
     */
    private array $db_selects = ['*'];

    /**
     * db query limit using LIMIT x, y | x
     * 
     * @since   0.9.0
     * @var     array
     */
    private array $db_limit = [];

    /**
     * db query order using ORDER
     * 
     * @since   0.9.0
     * @var     string
     */
    private string $db_order = '`id` DESC';

    /**
     * is collector initialized or not
     * 
     * @since   0.9.0
     * @var     bool
     */
    private bool $initialize = false;

    /**
     * class constructor
     * 
     * @param   int|WP_User|array   $target     user-id | user | list of receivers like: [[ 'name', 'data' ]]
     * @since   0.9.0
     */
    public function __construct($target)
    {
        if (is_int($target) || $target instanceof \WP_User) {
            $this->user_identity = User::get_identity($target);
        } elseif (is_array($target)) {
            $this->user_identity = $target;
        }
    }

    /**
     * set limit
     * 
     * @since   0.9.0
     * @param   array   $limit
     * @return  void
     */
    public function set_limit(array $limit): void
    {
        $this->db_limit = $limit;
    }

    /**
     * set limit
     * 
     * @since   0.9.0
     * @param   string   $order
     * @return  void
     */
    public function set_order(string $order): void
    {
        $this->db_order = $order;
    }

    /**
     * set selects
     * 
     * @since   0.9.0
     * @param   array   $select
     * @return  void
     */
    public function set_select(array $select): void
    {
        $this->db_selects = $select;
    }

    /**
     * init collector
     * 
     * @since   0.9.0
     * @return  void
     */
    public function init(): void
    {
        $this->db_table_name = Db::table_name('notifs');
        $this->db_query      = $this->build_query();

        $this->initialize = true;
    }

    /**
     * get notifs full table name
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_db_table_name(): string
    {
        return Db::table_name($this->db_table_name);
    }

    /**
     * build where query for db select
     * 
     * @since   0.9.0
     * @return  array
     */
    public function build_where_query(): array
    {
        if (empty($this->user_identity)) {
            return '';
        }

        $targets = [];
        $values  = [];

        foreach ($this->user_identity as $identity) {
            $id_string = json_encode($identity);

            $targets[] = "JSON_CONTAINS(`recipients`, '%s')";
            $values[]  = $id_string;
        }

        return [ implode(' OR ', $targets), $values ];
    }

    /**
     * build limit query for db select
     * 
     * @since   0.9.0
     * @return  string
     */
    public function build_limit_query(): string
    {
        $count = count($this->db_limit);

        if ($count === 1) {
            $query = (string) $this->db_limit[0];
        } elseif ($count === 2) {
            $query = implode(', ', $this->db_limit);
        } else {
            $query = '';
        }

        /**
         * filter: database limit query manage
         * 
         * @since   0.9.0
         * @param   array    $limit
         * @param   string   $query
         */
        return apply_filters('wpnb_collect_limit', $query, $this->db_limit);
    }

    /**
     * build order query for db select
     * 
     * @since   0.9.0
     * @return  string
     */
    public function build_order_query(): string
    {
        $query = empty($this->db_order) ? '' : $this->db_order;

        /**
         * filter: database order query manage
         * 
         * @since   0.9.0
         * @param   array    $order
         * @param   string   $query
         */
        return (string) apply_filters('wpnb_collect_order', $query, $this->db_order);
    }

    /**
     * build db query to get results
     * 
     * @since   0.9.0
     * @return  string
     */
    public function build_query(): string
    {
        $query = [];

        // #select
        $query[] = sprintf('SELECT %s', implode(', ', $this->db_selects));

        // #from
        $query[] = sprintf('FROM `%s`', $this->db_table_name);

        // #where
        [$where_query, $where_values] = $this->build_where_query();

        if (!empty($where_query)) {
            $query[] = sprintf('WHERE %s', $where_query);
        }

        // #order
        $order_query = $this->build_order_query();
        
        if (!empty($order_query)) {
            $query[] = sprintf('ORDER BY %s', $order_query);
        }

        // #limit
        $limit_query = $this->build_limit_query();

        if (!empty($limit_query)) {
            $query[] = sprintf('LIMIT %s', $limit_query);
        }

        global $wpdb;

        $fetch = $wpdb->prepare(implode(' ', $query), ...$where_values);

        /**
         * filter: database order query manage
         * 
         * @since   0.9.0
         * @param   string   $query
         */
        return apply_filters('wpnb_collect_query', $fetch) . ';';
    }


    /**
     * get database data
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_db(): array
    {
        if (!$this->initialize || empty($this->db_query)) {
            return [];
        }

        global $wpdb;

        $results = $wpdb->get_results($this->db_query);

        /**
         * filter: database get db results
         * 
         * @since   0.9.0
         * @param   array   $results
         */
        return apply_filters('wpnb_collect_get_db', $results);
    }

    /**
     * get notifications
     * 
     * @since   0.9.0
     * @return  array<Notification>
     */
    public function get(): array
    {
        $notifs = $this->get_db();
        $result = [];

        if (empty($notifs)) {
            return [];
        }

        foreach ($notifs as $notif) {
            $result[] = new Notification($notif);
        }

        /**
         * filter: database get db results
         * 
         * @since   0.9.0
         * @param   array   $result
         */
        return apply_filters('wpnb_collect_get', $result);
    }
}