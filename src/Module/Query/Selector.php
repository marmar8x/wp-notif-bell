<?php

namespace Irmmr\WpNotifBell\Module\Query;

// If this file is called directly, abort.
defined('WPINC') || die;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Select;

/**
 * Class Selector
 * select query builder
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Module\Query
 */
class Selector extends Query
{
    /**
     * class constructor
     * - Can use custom builder and selector
     * 
     * @since   0.9.0
     * @param   string                  $table_name     Table name
     * @param   GenericBuilder|null     $builder        Set a builder
     * @param   Select|null             $selector       Set a selector
     */
    public function __construct(string $table_name, ?GenericBuilder $builder = null, ?Select $selector = null)
    {
        $this->table_name = $table_name;

        $this->set_builder($builder ?? new GenericBuilder);
        $this->set_selector($selector ?? $this->builder->select($this->table_name));
    }

    /**
     * set selector option [!!]
     * 
     * @since   0.9.0
     * @param   Select  $selector
     * @return  void
     */
    public function set_selector(Select $selector): void
    {
        $this->container = $selector;
    }

    /**
     * get selector
     * 
     * @since   0.9.0
     * @return  Select
     */
    public function selector(): Select
    {
        return $this->container;
    }
}