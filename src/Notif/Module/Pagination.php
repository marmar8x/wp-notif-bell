<?php

namespace Irmmr\WpNotifBell\Notif\Module;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Notif\Collector;

/**
 * Class Notification
 * a class for every notification with all
 * notif data
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Module
 */
class Pagination
{
    /**
     * collector, main notif collector
     * 
     * @since   0.9.0
     * @var     Collector   $collector
     */
    protected Collector $collector;

    /**
     * current page number
     * 
     * @since   0.9.0
     * @var     int     $page
     */
    protected int $page = 1;

    /**
     * notifications per page
     * 
     * @since   0.9.0
     * @var     int     $per_page
     */
    protected int $per_page = 5;

    /**
     * pagination class constructor
     * 
     * @since   0.9.0
     * @param   Collector   $collector
     */
    public function __construct(Collector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * set per page
     * 
     * @since   0.9.0
     * @param   int     $per_page
     * @return  self
     */
    public function per_page(int $per_page): self
    {
        $this->per_page = $per_page;

        return $this;
    }

    /**
     * set page number
     * 
     * @since   0.9.0
     * @param   int     $page
     * @return  self
     */
    public function page(int $page): self
    {
        if ($page <= 0) {
            $page = 1;
        }
        
        $this->page = $page;

        return $this;
    }

    // data values

    /**
     * get pages count
     * 
     * @since   0.9.0
     * @return  int
     */
    public function get_pages_count(): int
    {
        $total_count = $this->collector->get_count();

        return ceil($total_count / $this->per_page);
    }

    /**
     * init all pagination
     * !! only run at the End
     * 
     * @since   0.9.0
     * @return  self
     */
    public function init(): self
    {
        $offset = ($this->page - 1) * $this->per_page;

        $this->collector->select()
            ->limit($offset, $this->per_page);

        return $this;
    }
}