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
 * Class EyeSet [rout] USER
 * set notifications status [seen|unseen]
 *
 * @since    1.0.0
 * @package  Irmmr\WpNotifBell\Api\Routes
 */
class EyeSet extends RoutAbstract
{
    // setting actions
    // @since   1.0.0
    protected const ACTIONS = ['seen-all', 'unseen-all', 'set'];

    /**
     * @RoutAbstract
     *
     * @since   1.0.0
     * @return  void
     */
    public function define(): void
    {
        register_rest_route(self::VER_1, '/eye/', [
            'methods'               => WP_REST_Server::EDITABLE,
            'callback'              => [$this, 'runCallback'],
            'permission_callback'   => [$this, 'checkPermission'],
            'args'                  => $this->getArguments()
        ]);
    }

    /**
     * check if user owns notif by id
     *
     * @since   1.0.0
     * @param   WP_User $user
     * @param   int     $id
     * @return  bool
     */
    protected function isUserNotifExists(WP_User $user, int $id): bool
    {
        $c = (new Collector)->target_by_user($user);

        $c->select()->where()->equals('id', $id);

        return $c->has();
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

        $eye = User::eye($user);

        $action = $request->get_param('action');

        if ($action === 'seen-all') {
            $eye->set_seen_all();

        } elseif ($action === 'unseen-all') {
            $eye->set_unseen_all();

        } elseif ($action === 'set') {
            if ($request->has_param('data')) {
                $data = $request->get_param('data');

                foreach ($data as $id => $status) {
                    if ($this->isUserNotifExists($user, $id)) {
                        $eye->set_status($status, $id);

                        $this->data[ $id ] = $status ? 'seen' : 'unseen';
                    }
                }
            }

        } else {
            return new WP_Error('invalid_action', __('Invalid action!', 'notif-bell'));
        }

        $this->data['action'] = $action;

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
            'action' => [
                'description'       => 'Setter action',
                'required'          => false,
                'type'              => 'string',
                'default'           => 'set',
                'validate_callback' => static fn ($param, $request, $key) => in_array($param, self::ACTIONS, true),
            ],
            'data' => [
                'description'   => 'Notification ID(s) data (array<{ [id: int]: bool }>)',
                'required'      => false,
                'type'          => 'array',
                'validate_callback' => static fn ($param, $request, $key) => is_array($param),
                'sanitize_callback' => function ($param, $request, $key) {
                    $fetch = [];

                    foreach ($param as $id => $status) {
                        if (!is_int($id) || !is_numeric($id)) {
                            continue;
                        }

                        $id     = absint($id);
                        $status = in_array($status, ['seen', 'true', 't', 'yes', 'y', '1'], true);

                        $fetch[$id] = $status;
                    }

                    return $fetch;
                },
            ]
        ];
    }
}