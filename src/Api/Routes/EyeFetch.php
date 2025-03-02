<?php

namespace Irmmr\WpNotifBell\Api\Routes;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Api\RoutAbstract;
use Irmmr\WpNotifBell\User;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_User;

/**
 * Class EyeFetch [rout] USER
 * get seen/unseen status of notifications
 *
 * @since    1.0.0
 * @package  Irmmr\WpNotifBell\Api\Routes
 */
class EyeFetch extends RoutAbstract
{
    /**
     * @RoutAbstract
     *
     * @since   1.0.0
     * @return  void
     */
    public function define(): void
    {
        register_rest_route(self::VER_1, '/eye/', [
            'methods'               => WP_REST_Server::READABLE,
            'callback'              => [$this, 'runCallback'],
            'permission_callback'   => [$this, 'checkPermission'],
            'args'                  => $this->getArguments()
        ]);
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

        $ids  = $request->get_param('id');

        if (is_numeric($ids)) {
            $list = [ intval($ids) ];

        } elseif (is_array($ids)) {
            $list = array_map('intval', $ids);
            $list = array_unique($list);
        } else {
            return new WP_Error('invalid_param', __('Invalid ID!', 'notif-bell'));
        }

        $eye    = User::eye($user);
        $fetch  = [];

        foreach ($list as $id) {
            $fetch[$id] = $eye->get_status($id) ? 'seen' : 'unseen';
        }

        $this->data = $fetch;

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
            'id' => [
                'description'   => 'Notification ID(s) (array<int> | int)',
                'required'      => true,
                'validate_callback' => static fn ($param, $request, $key) => is_numeric($param) || is_array($param)
            ]
        ];
    }
}