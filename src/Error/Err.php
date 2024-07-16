<?php

namespace Irmmr\WpNotifBell\Error;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Error
 * error unit includes all information
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Error
 */
class Err
{
    /**
     * error code [slug]
     * 
     * @var     string
     * @since   0.9.0
     */
    private string $code;

    /**
     * error message
     * 
     * @var     string
     * @since   0.9.0
     */
    private string $message = '';

    /**
     * error data
     * 
     * @var     array
     * @since   0.9.0
     */
    private array $data = [];

	/**
	 * Initializes the error.
	 *
	 * @since   0.9.0
	 * @param   string      $code    Error code.
	 * @param   string      $message Error message.
	 * @param   array       $data    Optional. Error data.
	 */
	public function __construct(string $code, string $message, array $data = []) {
        $this->code     = $code;
        $this->message  = $message;
        $this->data     = $data;
	}

    /**
     * create an instance for Err class
     * 
     * @since   0.9.0
     * @return  self
     */
    public static function _(string $code, string $message, array $data = []): self
    {
        return new Err($code, $message, $data);
    }

    /**
     * verify error data
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function verify(): bool
    {
        return !empty($this->code);
    }

    /**
     * @since   0.9.0
     * @return  string
     */
    public function get_code(): string
    {
        return $this->code;
    }

    /**
     * @since   0.9.0
     * @return  void
     */
    public function set_code(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @since   0.9.0
     * @return  string
     */
    public function get_msg(): string
    {
        return $this->message;
    }

    /**
     * @since   0.9.0
     * @return  void
     */
    public function set_msg(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @since   0.9.0
     * @return  array
     */
    public function get_data(): array
    {
        return $this->data;
    }

    /**
     * @since   0.9.0
     * @return  void
     */
    public function set_data(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @since   0.9.0
     * @return  bool
     */
    public function has_data(): bool
    {
        return !empty($this->data);
    }

    /**
     * @since   0.9.0
     * @return  array
     */
    public function get_array(): array
    {
        return [
            'code'  => $this->code,
            'msg'   => $this->message,
            'data'  => $this->data,
        ];
    }
}