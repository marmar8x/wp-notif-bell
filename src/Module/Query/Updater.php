<?php

namespace Irmmr\WpNotifBell\Module\Query;

// If this file is called directly, abort.
defined('WPINC') || die;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Update;

/**
 * Class Updater
 * update query builder
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Module\Query
 */
class Updater extends Query
{
    /**
     * class constructor
     * - Can use custom builder and selector
     * 
     * @since   0.9.0
     * @param   string                  $table_name     Table name
     * @param   GenericBuilder|null     $builder        Set a builder
     * @param   Update|null             $updater        Set a updater
     */
    public function __construct(string $table_name, ?GenericBuilder $builder = null, ?Update $updater = null)
    {
        $this->table_name = $table_name;

        $this->set_builder($builder ?? new GenericBuilder);
        $this->set_updater($updater ?? $this->builder->update($this->table_name));
    }

    /**
     * set updater option [!!]
     * 
     * @since   0.9.0
     * @param   Update  $updater
     * @return  void
     */
    public function set_updater(Update $updater): void
    {
        $this->container = $updater;
    }

    /**
     * get updater
     * 
     * @since   0.9.0
     * @return  Update
     */
    public function updater(): Update
    {
        return $this->container;
    }
}