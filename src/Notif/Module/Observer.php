<?php

namespace Irmmr\WpNotifBell\Notif\Module;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Module\QuerySelector;
use Irmmr\WpNotifBell\Notif\Collector;

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
    protected const META_KEY = 'wpnb_list_seen';

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
     * @var     \WP_User    $user
     */
    protected \WP_User $user;

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
     * @since   0.9.0
     * @param   Collector   $collector
     */
    public function __construct(\WP_User $user, Collector $collector)
    {
        $this->user      = $user;
        $this->collector = $collector;
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

        // get database prefix
        $db_prefix  = $wpdb->prefix;

        // get `usermeta` table name
        $user_meta_table = $db_prefix . 'usermeta';

        // create new mysql query
        $meta_query = new QuerySelector($user_meta_table);

        // user query selector
        // trying to search in seen list for notif id
        $meta_query->selector()
            ->count()
            ->where()
                ->equals('meta_key', self::META_KEY)
                ->equals('user_id', $this->user->ID)
                ->asLiteral('find_in_set(wp_nb_notifs.id,wp_usermeta.meta_value)')
                ->end();
        
        // create final query with prepared values
        $meta_query_str = $meta_query->get_prepared([
            'semicolon' => false
        ]);

        // define type of final statement
        // tou want list all unseen notifs by user
        // or only seen notifs
        if ($this->filter_type === self::FILTER_TYPE_UNSEEN) {
            $final_query = "({$meta_query_str}) = 0";
        } else {
            $final_query = "({$meta_query_str}) > 0";
        }

        // add all query as a `where` condition to main collector
        $this->collector->select()
            ->where()
                ->asLiteral($final_query)
                ->end();

        return $this;
    }
}