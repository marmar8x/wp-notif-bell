<?php

namespace Irmmr\WpNotifBell\Notif;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\Error\Err;
use Irmmr\WpNotifBell\Error\Stack;
use Irmmr\WpNotifBell\Module\Query\Remover as Query;
use Irmmr\WpNotifBell\Notif\Module\Database;
use Irmmr\WpNotifBell\Traits\ResultTrait;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;

/**
 * Class Remover
 * remove or dump any notif
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif
 */
final class Remover
{
    use ResultTrait;

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
     * 
     * @since   0.9.0
     */
    public function __construct()
    {
        $this->table_name   = Db::table_name('notifs');

        $this->query        = new Query($this->table_name);
        $this->errors       = new Stack;
        $this->db           = new Database;
    }

    /**
     * get remover
     * 
     * @since   0.9.0
     * @return  Delete
     */
    public function delete(): Delete
    {
        return $this->query->delete();
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
     * find notif by key
     * 
     * @since   0.9.0
     * @param   string  $key
     * @return  self
     */
    public function find_by_key(string $key): self
    {
        $this->delete()
            ->where()
                ->equals('key', $key)
                ->end();

        return $this;
    }

    /**
     * check notif is removed
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function is_removed(): bool
    {
        return $this->result['status'] === 'removed';
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
     * run notif remover
     * 
     * @since   0.9.0
     * @return  void
     */
    public function run(): void
    {
        // check if notif removed before
        if ($this->is_removed()) {
            $this->errors->add( Err::_('removed_before', 'This notif is removed before.') );
        }

        // simple errors, use it faster
        $err = $this->errors;

        // check for errors
        if ($err->has()) {
            $this->set_result('error', $err->get());

            return;
        }

        // set results
        $result = $this->db->run_query( $this->get_query(), $this->query->get_builder_values() );

        // check for wpdb error
        $wpdb_error = $this->db->get_error();

        if (!empty($wpdb_error)) {
            $this->errors->add( Err::_('wpdb_error', $wpdb_error) );
        }

        // query error
        if (!$result) {
            $this->errors->add( Err::_('run_error', 'failed run query.') );
        }

        if ($err->has()) {
            $this->set_result('error', $err->get());
        } else {
            $this->set_result('removed');
        }
    }
}