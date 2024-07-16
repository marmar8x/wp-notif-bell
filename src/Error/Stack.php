<?php

namespace Irmmr\WpNotifBell\Error;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Stack
 * a class for collect all errors
 * in an application
 * 
 * id           string slug
 * level        Error Levels
 * message      string
 * data         array
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Error
 */
class Stack
{
    /**
     * list of all errors collected
     * 
     * @since   0.9.0
     * @var     array<Err>
     */
    private array $errors = [];

    /**
     * add an error to stack
     * 
     * @since   0.9.0
     * @param   Err   $error
     * @return  void
     */
    public function add(Err $error): void
    {
        if ($error->verify()) {
            $this->errors[] = $error;
        }
    }

    /**
     * get list of all errors
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get(): array
    {
        return $this->errors;
    }

    /**
     * check for errors
     * 
     * @since   0.9.0
     * @return  bool
     */
    public function has(): bool
    {
        return !empty($this->errors);
    }

    /**
     * get errors count
     * 
     * @since   0.9.0
     * @return  int
     */
    public function count(): int
    {
        return count($this->errors);
    }

    /**
     * remove all errors
     * 
     * @since   0.9.0
     * @return  void
     */
    public function reset(): void
    {
        $this->errors = [];
    }
}