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

        $count = $this->get_pages_count();

        if ($page > $count) {
            $page = $count;
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

        if ($offset < 0) {
            $offset = 0;
        }

        $this->collector->select()
            ->limit($offset, $this->per_page);

        return $this;
    }

    /**
     * get all pages for using in pagination nav
     * !! The output of this function is in the same format, but the way it works will be improved.
     * 
     * @since   0.9.0
     * @param   array   $options
     *  - ceil   => int
     *  - btns   => bool
     *  - pages  => bool
     * @return  array
     *  - type   => string  page|dot|prev-btn|next-btn
     */
    public function get_pages_list(array $options = [
        'ceil'  => 2,
        'btns'  => true
    ]): array
    {
        $count  = $this->get_pages_count();
        $active = $this->page;
        $pages  = [];

        // get options: ceil    int, 0 for disable
        $ceil = intval($options['ceil'] ?? 3);

        // hidden pages with ceil
        $hidden_pages = [];

        // apply ceil on pages
        if (0 !== $ceil) {
            for ($i = $count - $ceil; $i > 1; $i --) {
                if (abs($active - $i - 1) > ($ceil - 1)) {
                    $hidden_pages[] = $i + 1;
                }
            }
        }

        // render every page
        for ($p = 1; $p <= $count; $p ++) {
            if (in_array($p, $hidden_pages)) {
                // do not duplicate dots
                if (!in_array($p - 1, $hidden_pages)) {
                    $pages[] = [ 'type' => 'dot' ];
                }
            } else {
                $pages[] = ['type'  => 'page', 'page' => $p];
            }
        }

        // check options: btns  =>  bool
        if (boolval($options['btns'])) {
            // add prev btn
            if ($active > 1) {
                array_unshift($pages, [ 'type' => 'prev-btn', 'page' => $active - 1 ]);
            }

            // add next btn
            if ($active < $count) {
                $pages[] = [ 'type' => 'next-btn', 'page' => $active + 1 ];
            }
        }

        return $pages;
    }
}