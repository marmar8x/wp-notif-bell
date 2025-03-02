<?php

namespace Irmmr\WpNotifBell\Api;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Interfaces\ApiInterface;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Class RoutAbstract [abstract]
 * abstract class for define every api rout
 *
 * @since    1.0.0
 * @package  Irmmr\WpNotifBell\Api
 */
abstract class RoutAbstract implements ApiInterface
{
    /**
     * response template
     *
     * @since   1.0.0
     * @var     array
     */
    protected array $response_temp = [
        'ok'        => false,
        'data'      => [],
        'errors'    => []
    ];

    /**
     * response data
     *
     * @since   1.0.0
     * @var     array
     */
    protected array $data   = [];

    /**
     * response errors
     *
     * @since   1.0.0
     * @var     array
     */
    protected array $errors = [];

    /**
     * create response based on template
     *
     * @since   1.0.0
     * @param   bool    $ok     ok or not?
     * @param   array   $data   any data!
     * @param   array   $errors any errors
     * @return  array
     */
    protected function create_response(bool $ok, array $data = [], array $errors = []): array
    {
        $response = $this->response_temp;

        $response['ok']     = $ok;
        $response['data']   = empty($data) ? $this->data : $data;
        $response['errors'] = empty($errors) ? $this->errors : $errors;

        return $response;
    }

    /**
     * class constructor
     *
     * @since   1.0.0
     */
    public function __construct()
    {
        $this->define();
    }

    /**
     * @return void
     */
    abstract public function define(): void;

    /**
     * run main callback for define rout
     *
     * @since   1.0.0
     * @param   WP_REST_Request $request
     * @return  WP_REST_Response|WP_Error
     */
    abstract public function runCallback(WP_REST_Request $request);

    /**
     * check permissions
     *
     * @since   1.0.0
     * @param   WP_REST_Request $request
     * @return  bool
     */
    abstract public function checkPermission(WP_REST_Request $request): bool;

    /**
     * get parameters data [arguments]
     *
     * @since   1.0.0
     * @return  array
     */
    abstract public function getArguments(): array;
}