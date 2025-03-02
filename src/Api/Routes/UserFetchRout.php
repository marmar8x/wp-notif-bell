<?php

namespace Irmmr\WpNotifBell\Api\Routes;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Api\RoutAbstract;
use Irmmr\WpNotifBell\Notif\Collector;
use Irmmr\WpNotifBell\User;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_User;

/**
 * Class UserFetchRout [rout] USER
 * use fetch/collector for getting notifications for current user
 *
 * @since    1.0.0
 * @package  Irmmr\WpNotifBell\Api\Routes
 */
class UserFetchRout extends RoutAbstract
{
    /**
     * @RoutAbstract
     *
     * @since   1.0.0
     * @return  void
     */
    public function define(): void
    {
        register_rest_route(self::VER_1, '/notifs/', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'runCallback'],
            'permission_callback'   => [$this, 'checkPermission'],
            'args'                  => $this->getArguments()
        ]);
    }

    /**
     * get seen/unseen counts
     *
     * @param   WP_User     $user
     * @param   Collector   $c
     * @param   string      $filter
     * @return  int
     * @since   1.0.0
     */
    protected function getObserverCount(WP_User $user, Collector $c, string $filter): int
    {
        // TODO: using __clone() directly is getting error from IDE, using "clone" itself is not really getting a cloned version of selector :/
        $mc = $c->__clone();

        $mc->observer($user)->filter($filter)->apply();

        return $mc->get_count();
    }

    /**
     * apply fetching filters
     *
     * @since   1.0.0
     * @param   WP_User             $user
     * @param   WP_REST_Request     $request
     * @param   WP_REST_Response    $response
     * @param   Collector           $c
     * @return  void
     */
    protected function applyFilters(WP_User $user, WP_REST_Request $request, WP_REST_Response $response, Collector $c): void
    {
        // start date
        if ($request->has_param('start_date')) {
            $start_date = strtotime( $request->get_param('start_date') );

            $c->select()->where()->greaterThanOrEqual('sent_at', $start_date);
        }

        // end date
        if ($request->has_param('end_date')) {
            $end_date = strtotime( $request->get_param('end_date') );

            $c->select()->where()->lessThanOrEqual('sent_at', $end_date);
        }

        // search
        if ($request->has_param('search')) {
            $search = trim( $request->get_param('search') );

            $c->select()->where()
                ->subWhere()
                ->like('title', "%{$search}%")
                ->like('content', "%{$search}%");
        }

        // tags
        if ($request->has_param('tags')) {
            $tags = (array) $request->get_param('tags');

            if (!empty( $tags )) {
                $c->target_by_tags($tags);
            }
        }

        // enable pagination
        if ($request->has_param('per_page')) {
            $per_page = intval( $request->get_param('per_page') );
            $page     = intval( $request->get_param('page') ?? 1 );

            $pagination = $c->pagination();
            $pagination->page($page)->per_page($per_page);

            $pagination->init();

            $this->data['pages']     = $pagination->get_pages_list();
            $this->data['page']      = $page;
            $this->data['per_page']  = $per_page;

            // set pages count  [X-WP-TotalPages]
            $response->header('X-WP-TotalPages', $pagination->get_pages_count());
        }

        // order & order-by
        if ($request->has_param('order') && $request->has_param('order_by')) {
            $c->select()->orderBy( $request->get_param('order_by'), $request->get_param('order') );
        }

        // set observer counts
        $response->header('X-WPNB-TotalSeen', $this->getObserverCount($user, $c, 'seen'));
        $response->header('X-WPNB-TotalUnseen', $this->getObserverCount($user, $c, 'unseen'));

        // observer
        if ($request->has_param('observer')) {
            $status = $request->get_param('observer');

            if ($status !== 'none') {
                $c->observer($user)->filter($status === 'seen' ? 'seen' : 'unseen')->apply();
            }
        }

        // total results count  [X-WP-Total]
        $response->header('X-WP-Total', $c->get_count());
    }

    /**
     * @RoutAbstract
     *
     * @since   1.0.0
     * @return  WP_REST_Response|WP_Error
     */
    public function runCallback(WP_REST_Request $request)
    {
        $user  = wp_get_current_user();

        // @error invalid_user
        if (!$user instanceof WP_User) {
            return new WP_Error('invalid_user', __('Invalid user!', 'notif-bell'));
        }

        // @response
        $response = new WP_REST_Response();

        // it's ok bro
        $response->set_status(200);

        // collector
        $c = (new Collector)
            ->target_by_user($user);

        $this->applyFilters($user, $request, $response, $c);

        // only count [check headers]
        if ($request->has_param('count') && $request->get_param('count') === true) {
            $this->data['count'] = $c->get_count();

            $response->set_data( $this->create_response(true) );

            return $response;
        }

        // collect
        $notifs = $c->fetch();

        // eye
        $eye = User::eye($user);

        $this->data['notifs'] = [];

        foreach ($notifs as $notif) {
            $this->data['notifs'][] = [
                'id'            => $notif->id,
                'key'           => $notif->key,
                'format'        => $notif->format,
                'title'         => $notif->title,
                'content'       => $notif->main_content,
                'sender'        => $notif->sender,
                'tags'          => $notif->tags,
                'send_date'     => $notif->send_date,
                'status'        => $eye->get_status($notif->id) ? 'seen' : 'unseen'
            ];
        }

        $response->set_data( $this->create_response(true) );

        return $response;
    }

    /**
     * @RoutAbstract
     *
     * @since   1.0.0
     * @param   WP_REST_Request $request
     * @return  bool
     */
    public function checkPermission(WP_REST_Request $request): bool
    {
        if (0 === get_current_user_id()) {
            return false;
        }

        return true;
    }

    /**
     * @RoutAbstract
     *
     * @since   1.0.0
     * @return  array
     */
    public function getArguments(): array
    {
        return [
            'page' => [
                'description'   => 'Pagination: Current page number [def = 1]',
                'required'      => false,
                'default'       => 1,
                'type'          => 'integer',
                'sanitize_callback' => 'absint'
            ],
            'per_page' => [
                'description'   => 'Pagination: Every page items limit [def = 10]',
                'required'      => false,
                'default'       => 10,
                'type'          => 'integer',
                'sanitize_callback' => 'absint'
            ],
            'observer' => [
                'description'   => 'Filter seen/unseen notifications [seem|unseen|none]',
                'required'      => false,
                'default'       => 'none',
                'type'          => 'string',
                'validate_callback' => static fn ($param, $request, $key) => $param === 'seen' || $param === 'unseen' || $param === 'none',
            ],
            'count' => [
                'description'   => 'Only send counts, not notifications',
                'required'      => false,
                'type'          => 'boolean'
            ],
            'order' => [
                'description'   => 'Fetching order',
                'required'      => false,
                'default'       => 'DESC',
                'type'          => 'string',
                'validate_callback' => static fn ($param, $request, $key) => $param === 'DESC' || $param === 'ASC',
            ],
            'order_by' => [
                'description'   => 'Fetching order-by',
                'required'      => false,
                'default'       => 'sent_at',
                'type'          => 'string',
                'validate_callback' => static fn ($param, $request, $key) => in_array($param, self::DB_COLUMNS, true),
            ],
            'start_date' => [
                'description' => 'Filter notifications after this date (YYYY-MM-DD)',
                'required'    => false,
                'type'        => 'date',
                'validate_callback' => static fn ($param, $request, $key) => strtotime($param) !== false,
            ],
            'end_date' => [
                'description' => 'Filter notifications before this date (YYYY-MM-DD)',
                'required'    => false,
                'type'        => 'date',
                'validate_callback' => static fn ($param, $request, $key) => strtotime($param) !== false,
            ],
            'search' => [
                'description' => 'Search notifications by title or content',
                'required'    => false,
                'type'        => 'string',
                'validate_callback' => static fn ($param, $request, $key) => is_string($param) && !empty($param),
                'sanitize_callback' => 'sanitize_text_field'
            ],
            'tags' => [
                'description' => 'Filter notifications using tags',
                'required'    => false,
                'default'     => [],
                'type'        => 'array',
                'validate_callback' => static fn ($param, $request, $key) => is_array($param),
            ],
        ];
    }
}