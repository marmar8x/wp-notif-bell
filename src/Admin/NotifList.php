<?php

namespace Irmmr\WpNotifBell\Admin;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Date;
use Irmmr\WpNotifBell\Notif\Assist\Tags;
use Irmmr\WpNotifBell\Notif\Collector;
use Irmmr\WpNotifBell\Notif\Remover;

/**
 * Class NotifList
 * notificaitons list handle
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Admin
 */
class NotifList extends \WP_List_Table
{
    /**
     * count of all items
     * - helps in pagination
     * 
     * @since   0.9.0
     * @var     int    $items_total_count
     */
    protected int $items_total_count;

    /**
     * [pagination] items per page
     * 
     * @since   0.9.0
     * @var     int    $per_page
     */
    protected int $per_page = 10;

    /**
     * main notif collector
     * 
     * @since   0.9.0
     * @var     Collector   $collector
     */
    protected Collector $collector;

    /**
     * initialize as we can't use __constructor
     * 
     * @since   0.9.0
     * @return  void
     */
    public function init(): void
    {
        $this->collector = new Collector;
    }

    /**
     * use `Collector` to fetch notifications
     * 
     * @since   0.9.0
     * @return  array
     */
    private function collect_notifs(): array
    {
        $collector = $this->collector;

        // If no sort, default to user_login
        $orderby = !empty($_GET['orderby']) ? $_GET['orderby'] : 'date';

        // date is sent_at!
        if ($orderby === 'date') {
            $orderby = 'sent_at';
        }

        // search fields
        $search = isset($_REQUEST['s']) ? $_REQUEST['s'] : false;

        // search in all fields
        if ($search) {
            $collector->select()
                ->where()
                ->subWhere('OR')
                    ->like('title', "%$search%")
                    ->like('sender', "%$search%")
                    ->like('tags', "%$search%")
                    ->like('key', "%$search%")
                    ->end();
        }

        // If no order, default to asc
        $order = !empty($_GET['order']) ? strtoupper($_GET['order']) : 'DESC';

        // check for invalid orders
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'DESC';
        }

        // for prevent db error: Unknown column 'wp_nb_notifs.X' in 'order clause'
        $valid_orders = [
            'id', 'key', 'title', 'content', 'tags', 'format',
            'sent_at', 'created_at', 'updated_at'
        ];

        if (!in_array($orderby, $valid_orders)) {
            $orderby = 'sent_at';
        }
        
        $collector->select()
            ->orderBy($orderby, $order);

        $collector->pagination()
            ->per_page($this->per_page)
            ->page($this->get_pagenum())
            ->init();

        return $collector->get();
    }

    /**
     * use `Collector` to get count
     * 
     * @since   0.9.0
     * @return  int
     */
    private function collect_notifs_count(): int
    {
        return $this->collector->get_count();
    }

    /**
     * @see     https://developer.wordpress.org/reference/classes/wp_list_table/get_columns/
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_columns(): array
    {
        $columns = [
            'cb'            => '<input type="checkbox" />',
            'title'         => __('Title', 'wp-notif-bell'),
            'sender'        => __('Sender', 'wp-notif-bell'),
            'tags'          => __('Tags', 'wp-notif-bell'),
            'recipients'    => __('Recievers', 'wp-notif-bell'),
            'date'          => __('Date', 'wp-notif-bell')
        ];

        return $columns;
    }

	/**
	 * Get bulk actions
     * 
     * @see     https://developer.wordpress.org/reference/classes/wp_list_table/get_bulk_actions/
	 *
     * @since   0.9.0
	 * @return  array
	 */
	public function get_bulk_actions(): array
    {
		return [
            'delete' => __('Delete', 'wp-notif-bell')
        ];
	}

	/**
	 * Process bulk actions
     * 	 *
     * @since   0.9.0
	 * @return  array
	 */
    protected function process_bulk_action(): void
    {
        $current_action = $this->current_action();

        if (!isset($_REQUEST['_wpnonce']) || empty($current_action)) {
            return;
        }

        // In our file that handles the request, verify the nonce.
        $nonce = esc_attr($_REQUEST['_wpnonce']);
        $action = 'bulk-' . $this->_args['plural'];

        if (!wp_verify_nonce($nonce, $action)) {
            wp_redirect(esc_url_raw(add_query_arg([
                'wpnb-msg' => 'list-bulk-nonce'
            ])));
    
            exit;
        }
        
        $result = 'none';

        // get all selected notifications
        $notifs = (array) ($_POST['keys'] ?? []);

        if (empty($notifs)) {
            $result = 'list-bulk-uns';
        } else {
            switch ($current_action) {
                case 'delete':
                    foreach ($notifs as $_notif) {
                        (new Remover)->find_by_key($_notif)->run();
                    } 
    
                    $result = 'list-bulk-removed';
                    break;
    
                default:
                    return;
                    break;
            }
        }

        wp_redirect(
            esc_url_raw(
                add_query_arg([
                    'wpnb-msg' => $result
                ])
            )
        );

        exit;
    }

    /**
     * Bind table with columns, data and all
     * 
     * @see     https://developer.wordpress.org/reference/classes/wp_list_table/prepare_items/
     * @see     https://developer.wordpress.org/reference/classes/wp_list_table/set_pagination_args/
     * 
     * @since   0.9.0
     * @return  void
     */
    public function prepare_items(): void
    {
        // get list of all columns
        $columns    = $this->get_columns();

        $primary    = 'title';
        $hidden     = [];
        $sortable   = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable, $primary];
        
        $this->items                = $this->collect_notifs();
        $this->items_total_count    = $this->collect_notifs_count();

        // bulk actions
        $this->process_bulk_action();

        // set pagination options
        $this->set_pagination_args([
            'total_items' => $this->items_total_count,
            'per_page'    => $this->per_page
        ]);

        // set all items from db results
        $this->items = $this->collect_notifs();

    }

    /**
     * column 'title', create row actions
     * 
     * @since   0.9.0
     * @param   mixed   $item
     * @return  string
     */
    public function column_title($item): string
    {
        $edit_url = add_query_arg([
            'action' => 'edit',
            'key'    => $item->key
        ], menu_page_url('wpnb-send', false));

        $actions = [
            'edit' => sprintf('<a href="%s">' . __('Edit', 'wp-notif-bell') . '</a>', $edit_url)
        ];
    
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">key:%2$s</span>%3$s',
            $item->title ?? '',
            $item->key ?? '',
            $this->row_actions($actions)
        );
    }

    /**
     * get sortable columns for order
     * 
     * @see     https://developer.wordpress.org/reference/classes/wp_list_table/get_sortable_columns/
     * 
     * @since   0.9.0
     * @return  array
     */
    protected function get_sortable_columns(): array
    {
        $sortable_columns = [
            'title'     => ['title', false],
            'sender'    => ['sender', false],
            'date'      => ['date', false]
        ];

        return $sortable_columns;
    }

    /**
     * get column data/content
     * 
     * @see     https://developer.wordpress.org/reference/classes/wp_list_table/column_default/
     * 
     * @since   0.9.0
     * @param   object|array    $item
     * @param   string          $column_name
     * @return  string
     */
    public function column_default($item, $column_name)
    {
        $fetch = $item->$column_name ?? '';

        if ($column_name === 'recipients') {
            return count( json_decode($fetch, true) );
        } elseif ($column_name === 'date') {
            $sent_date   = $fetch = Date::to_i18n($item->sent_at);

            $create_date = Date::to_i18n($item->created_at);
            $update_date = Date::to_i18n($item->updated_at);

            if ($sent_date !== $create_date) {
                $fetch .= '<b>';
                $fetch .= '<br />' . __('Created at', 'wp-notif-bell') . '<br />';
                $fetch .= $create_date;
                $fetch .= '</b>';
            }

            if ($create_date !== $update_date) {
                $fetch .= '<b>';
                $fetch .= '<br />' . __('Modified at', 'wp-notif-bell') . '<br />';
                $fetch .= $update_date;
                $fetch .= '</b>';
            }
        } elseif ($column_name === 'tags') {
            $tag_list   = Tags::parse($fetch);
            $tag_slice  = array_slice($tag_list, 0, 3);
            $tag_count  = count($tag_list);
            
            if ($tag_count === 0) {
                $fetch = '-';
            } elseif ($tag_count <= 3) {
                $fetch = implode(', ', $tag_slice);
            } elseif ($tag_count > 3) {
                $fetch = implode(', ', $tag_slice);
                $fetch .= '<br /> +';
                $fetch .= sprintf( __('%d more', 'wp-notif-bell'), $tag_count - 3 );
            }
        } elseif ($column_name === 'title') {
            $fetch = $item->title;
        }

        return $fetch;
    }

    /**
     * get checkbox element for each item
     * 
     * @see     https://developer.wordpress.org/reference/classes/wp_list_table/column_cb/
     * 
     * @since   0.9.0
     * @param   array   $item
     * @return  string
     */
    public function column_cb($item): string
    {
        return sprintf(
            '<input type="checkbox" name="keys[]" value="%s" />',
            $item->key ?? ''
        );
    }

    /**
     * render all display html code
     *
     * @since   0.9.0
     * @return  void
     */
    public function render(): void
    {
        $this->init();
        $this->prepare_items();

        // echo simple css code
        echo '<style>.tablenav .actions {margin-top: unset !important;} .wpnb-key-list-h {font-size: 0.7rem; background: none !important; border: 0 !important;}</style>';

        echo '<div class="wrap">';

        echo '<form method="POST">';
        $this->search_box(__('Search', 'wp-notif-bell'), 'wpnb_list_search');
        echo '</form>';

        echo '<form method="POST">';
        $this->display();
        echo '</form>';

        echo '</div>';
    }
}