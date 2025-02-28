<?php

namespace Irmmr\WpNotifBell\Notif\Module;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\Interfaces\UserInterface;
use Irmmr\WpNotifBell\Logger;
use Irmmr\WpNotifBell\Module\Query\Selector as QuerySelector;
use Irmmr\WpNotifBell\Notif\Collector;
use Irmmr\WpNotifBell\User\Eye;
use WP_User;

/**
 * Class Observer
 * handle seen/unseen filters and counts
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Module
 */
class Observer
{
    // filter types
    // @since 0.9.0
    protected const FILTER_TYPE_SEEN    = 'seen';
    protected const FILTER_TYPE_UNSEEN  = 'unseen';
    protected const FILTER_TYPES        = [self::FILTER_TYPE_SEEN, self::FILTER_TYPE_UNSEEN];

    // the meta_key name for `usermeta` where we store
    // seen notif ids
    // @since 0.9.0
    protected const META_KEY = UserInterface::SEEN_META_KEY;

    /**
     * collector, main notif collector
     * 
     * @since   0.9.0
     * @var     Collector   $collector
     */
    protected Collector $collector;

    /**
     * user to watch it
     * 
     * @since   0.9.0
     * @var     WP_User    $user
     */
    protected WP_User $user;

    /**
     * eye of user
     *
     * @since   1.0.0
     * @var     Eye    $eye
     */
    protected Eye $eye;

    /**
     * filter type [seen|unseen]
     * 
     * @since   0.9.0
     * @var     string $filter_type
     */
    protected string $filter_type = self::FILTER_TYPE_UNSEEN;

    /**
     * class constructor
     *
     * @param   WP_User    $user
     * @param   Collector   $collector
     * @since   0.9.0
     */
    public function __construct(WP_User $user, Collector $collector)
    {
        $this->user      = $user;
        $this->collector = $collector;
        $this->eye       = new Eye($user);
    }

    /**
     * set a filter for observer
     * 
     * @since   0.9.0
     * @param   string $type
     * @return  self
     */
    public function filter(string $type): self
    {
        if (!in_array($type, self::FILTER_TYPES)) {
            return $this;
        }

        $this->filter_type = $type;

        return $this;
    }

    /**
     * get filter type
     * 
     * @since   0.9.0
     * @return  string
     */
    public function get_filter(): string
    {
        return $this->filter_type;
    }

    /**
     * apply all configs on collector
     * !! only run at the End
     * 
     * @since   0.9.0
     * @return  self
     */
    public function apply(): self
    {
        global $wpdb;

        // checking Eye data method
        $method = $this->eye->get_seen_method();

        // get database prefix
        //$db_prefix  = $wpdb->prefix;

        // get `usermeta` table name
        $user_meta_table = $wpdb->usermeta;

        // get notifs table name
        $notifs_table = Db::get_name('notifs', $wpdb->prefix);

        // create new mysql query
        $meta_query = new QuerySelector($user_meta_table);

        // user query selector
        // trying to search in seen list for notif id
        $selecting = $meta_query->selector()
            ->count()
            ->where()
                ->equals('meta_key', self::META_KEY)
                ->equals('user_id', $this->user->ID);

        if ($method === Eye::BY_COMMA) {
            $selecting->asLiteral("find_in_set({$notifs_table}.id,{$user_meta_table}.meta_value)")->end();

            $meta_query_str = $meta_query->get_prepared([
                'semicolon' => false
            ]);

            if ($this->filter_type === self::FILTER_TYPE_UNSEEN) {
                $final_query = "({$meta_query_str}) = 0";
            } else {
                $final_query = "({$meta_query_str}) > 0";
            }

        } elseif ($method === Eye::BY_BIN) {
            $bin_status = $this->filter_type === self::FILTER_TYPE_UNSEEN ? '0' : '1';
            $selecting->asLiteral("IF(LENGTH({$user_meta_table}.meta_value) < {$notifs_table}.id, 0, SUBSTRING(REVERSE({$user_meta_table}.meta_value) ,{$notifs_table}.id,1)) = '{$bin_status}'")->end();

            $meta_query_str = $meta_query->get_prepared([
                'semicolon' => false
            ]);

            $final_query = "({$meta_query_str}) > 0";

        } else {
            Logger::add('X: Error when looking for data method. (observer, eye)', Logger::N_MAIN, Logger::LEVEL_ERROR);

            return $this;
        }

        // add all query as a `where` condition to main collector
        $this->collector->select()
            ->where()
                ->asLiteral($final_query)
                ->end();

        return $this;
    }
}