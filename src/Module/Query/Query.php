<?php

namespace Irmmr\WpNotifBell\Module\Query;

// If this file is called directly, abort.
defined('WPINC') || die;

use NilPortugues\Sql\QueryBuilder\Builder\GenericBuilder;

/**
 * Class Query  [abstract]
 * main query class for create mysql queries
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Module\Query
 */
abstract class Query
{
    // @since   0.9.0
    public const OPTION = [
        'semicolon'     => true,
        'render_values' => true
    ];

    /**
     * table name
     * database table name
     * 
     * @since   0.9.0
     * @var     string  $table_name
     */
    protected string $table_name;

    /**
     * define classic builder from `query builder`
     * 
     * @see     https://github.com/nilportugues/php-sql-query-builder/blob/master/src/Builder/GenericBuilder.php
     * 
     * @since   0.9.0
     * @var     GenericBuilder  $builder
     */
    protected GenericBuilder $builder;

    /**
     * query holder for created apt
     * container holding main selectors for builder
     * 
     * @since   0.9.0
     * @var     Select|Delete|Update    $handler
     */
    public $container;

    /**
     * class constructor
     * - Can use custom builder and selector
     * 
     * @since   0.9.0
     * @param   string                  $table_name     Table name
     * @param   GenericBuilder|null     $builder        Set a builder
     * @param   Select|null             $selector       Set a selector
     */
    public function __construct(string $table_name, ?GenericBuilder $builder = null)
    {
        $this->table_name = $table_name;

        $this->set_builder($builder ?? new GenericBuilder);
    }

    /**
     * set builder option [!!]
     * 
     * @since   0.9.0
     * @param   GenericBuilder $builder
     * @return  void
     */
    public function set_builder(GenericBuilder $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * get builder
     * 
     * @since   0.9.0
     * @return  GenericBuilder
     */
    public function builder(): GenericBuilder
    {
        return $this->builder;
    }

    /**
     * get query that written by `nilportugues/php-sql-query-builder` library
     * this library uses :v1, :v2, :vx formats for prepare values in db
     * Using wp prepare values ​​only requires print formatted variables
     * :vx => %s
     * 
     * @since   0.9.0
     * @param   array   $option  Get options
     * @return  string
     */
    public function get(array $option = self::OPTION): string
    {
        $option = array_merge(self::OPTION, $option);

        $builder = $this->builder;

        $query  = $builder->writeFormatted($this->container);
        if ($option['semicolon']) {
            $query .= ';';
        }

        if ($option['render_values']) {
            // get builder values
            $values = $builder->getValues();

            $query = preg_replace_callback('/:v(\d+)/i', function ($m) use ($values) {
                $variable = $m[0] ?? '';
    
                if (!array_key_exists($variable, $values)) {
                    return '%s';
                }
    
                $value   = $values[$variable];
                $type    = gettype($value);
    
                if ($type === 'integer') {
                    return '%d';
                }
    
                return '%s';
            }, $query);
        }

        return $query;
    }

    /**
     * get prepared value query
     * 
     * @since   0.9.0
     * @param   array   $option  Get options
     * @return  string
     */
    public function get_prepared(array $option = self::OPTION): string
    {
        global $wpdb;

        // force rendering values for prepared query
        $option['render_values'] = true;

        return $wpdb->prepare($this->get($option), $this->get_builder_values());
    }

    /**
     * get builder values (prepares)
     * as mentioned earlier, we need values ​​in print format type.
     * 
     * @since   0.9.0
     * @return  array
     */
    public function get_builder_values(): array
    {
        return array_values($this->builder->getValues());
    }
}