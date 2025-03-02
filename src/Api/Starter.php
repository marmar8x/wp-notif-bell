<?php

namespace Irmmr\WpNotifBell\Api;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Starter
 * rest api starter [define]
 *
 * @since    1.0.0
 * @package  Irmmr\WpNotifBell\Api
 */
class Starter
{
    /**
     * start routes of api
     *
     * @since   1.0.0
     * @var     array
     */
    protected array $default_routes = [
        Routes\UserFetchRout::class,
        Routes\EyeFetch::class,
        Routes\EyeSet::class
    ];

    /**
     * class constructor
     *
     * @since   1.0.0
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'define']);
    }

    /**
     * define routes [using: $this, action: rest_api_init]
     *
     * @since   1.0.0
     * @return  void
     */
    public function define(): void
    {
        $routes = $this->default_routes;

        /**
         * filter of TextMagic defined variables
         *
         * @since   0.9.0
         * @param   array   $routes   list of defined variables like:   name<string> => data<callable|string>
         */
        $routes = (array) apply_filters('wpnb_api_routes', $routes);

        // run every rout that defined
        foreach ($routes as $route) {
            new $route;
        }
    }
}