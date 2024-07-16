<?php

namespace Irmmr\WpNotifBell\Module\Query;

// If this file is called directly, abort.
defined('WPINC') || die;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;
use NilPortugues\Sql\QueryBuilder\Manipulation\Delete;

/**
 * Class Remover
 * delete query builder
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Module\Query
 */
class Remover extends Query
{
    /**
     * class constructor
     * - Can use custom builder and selector
     * 
     * @since   0.9.0
     * @param   string                  $table_name     Table name
     * @param   GenericBuilder|null     $builder        Set a builder
     * @param   Delete|null             $delete         Set a remover
     */
    public function __construct(string $table_name, ?GenericBuilder $builder = null, ?Delete $delete = null)
    {
        $this->table_name = $table_name;

        $this->set_builder($builder ?? new GenericBuilder);
        $this->set_remover($delete ?? $this->builder->delete($this->table_name));
    }

    /**
     * set remover option [!!]
     * 
     * @since   0.9.0
     * @param   delete  $delete
     * @return  void
     */
    public function set_remover(Delete $delete): void
    {
        $this->container = $delete;
    }

    /**
     * get remover
     * 
     * @since   0.9.0
     * @return  Delete
     */
    public function delete(): Delete
    {
        return $this->container;
    }
}