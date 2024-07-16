<?php

namespace Irmmr\WpNotifBell\Traits;

use Irmmr\WpNotifBell\Error\Err;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Trait Result
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Traits
 */
trait ResultTrait
{
    // @since 0.9.0
    protected const DEFAULT_RESULT = [
        'status' => 'none',
        'errors' => [],
        'data'   => []
    ];

    /**
     * main resaults
     * 
     * @since   0.9.0
     * @var     array
     */
    protected array $result = self::DEFAULT_RESULT;

    /**
     * set sender resault to check by others
     * 
     * @since   0.9.0
     * @param   string  $status
     * @param   array   $errors
     * @param   array   $data
     * @return  void
     */
    protected function set_result(string $status, array $errors = [], array $data = []): void
    {
        $this->result = [
            'status' => $status,
            'errors' => $errors,
            'data'   => $data
        ];
    }

    /**
     * reset result
     * 
     * @since   0.9.0
     * @return  void
     */
    protected function reset_result(): void
    {
        $this->result = self::DEFAULT_RESULT;
    }

    /**
     * get result
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_result(): array
    {
        return $this->result;
    }

    /**
     * get result suitable for ajax requests
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_result_esc(): array
    {
        $result = $this->result;
        $errors = [];
        
        // convert error stdClasses to an usable array
        foreach ($result['errors'] as $error) {
            if ($error instanceof Err) {
                $errors[] = $error->get_array();
            }
        }

        $result['errors'] = $errors;

        return $result;
    }

    /**
     * get result 'data'
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_result_data(): array
    {
        return $this->result['data'] ?? [];
    }

    /**
     * get result 'errors'
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_result_errors(): array
    {
        return $this->result['errors'] ?? [];
    }
}