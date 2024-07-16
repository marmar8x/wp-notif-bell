<?php

namespace Irmmr\WpNotifBell\Helpers;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Class Option
 * wordpress option set/update/get/delete
 * I was always hesitant to add such classes.
 * This class is more for creating confidence and cleaner writing.
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Helpers
 */
class Option
{
    /**
     * add an option
     * 
     * You do not need to serialize values. If the value needs to be serialized,
     * then it will be serialized before it is inserted into the database.
     * Remember, resources cannot be serialized or added as an option.
     * @see https://developer.wordpress.org/reference/functions/add_option/
     * 
     * @since   0.9.0
     * @param   string      $option     Name of the option to add. Expected to not be SQL-escaped.
     * @param   mixed       $value      Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
     * @param   bool|null   $autoload   Whether to load the option when WordPress starts up.
     * @return  bool
     */
    public static function add(string $option, $value = '', ?bool $autoload = null): bool
    {
        return add_option($option, $value, $autoload);
    }

    /**
     * update an option
     * 
     * You do not need to serialize values. If the value needs to be serialized,
     * then it will be serialized before it is inserted into the database.
     * Remember, resources cannot be serialized or added as an option.
     * @see https://developer.wordpress.org/reference/functions/update_option/
     * 
     * @since   0.9.0
     * @param   string      $option     Name of the option to update. Expected to not be SQL-escaped.
     * @param   mixed       $value      Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
     * @param   bool|null   $autoload   Whether to load the option when WordPress starts up.
     * @return  bool
     */
    public static function update(string $option, $value, ?bool $autoload = null): bool
    {
        return update_option($option, $value, $autoload);
    }

    /**
     * delete an option
     * 
     * Removes an option by name. Prevents removal of protected WordPress options.
     * @see https://developer.wordpress.org/reference/functions/delete_option/
     * 
     * @since   0.9.0
     * @param   string      $option     Name of the option to delete. Expected to not be SQL-escaped.
     * @return  bool
     */
    public static function delete(string $option): bool
    {
        return delete_option($option);
    }

    /**
     * get an option
     * 
     * If the option does not exist, and a default value is not provided, boolean false is returned.
     * This could be used to check whether you need to initialize an option during installation of
     * a plugin, however that can be done better by using add_option() which will not overwrite
     * existing options.
     * @see https://developer.wordpress.org/reference/functions/get_option/
     * 
     * @since   0.9.0
     * @param   string      $option          Name of the option to get. Expected to not be SQL-escaped.
     * @param   mixed       $default_value   Default value to return if the option does not exist.
     * @return  mixed
     */
    public static function get(string $option, $default_value = false)
    {
        return get_option($option, $default_value);
    }

    /**
     * check if option exists
     * 
     * @since   0.9.0
     * @param   string  $option  Name of the option to check. Expected to not be SQL-escaped.
     * @return  bool
     */
    public static function exists(string $option): bool
    {
        return self::get($option) !== false;
    }

    /**
     * set an option
     * 
     * @since   0.9.0
     * @param   string      $option     Name of the option to set. Expected to not be SQL-escaped.
     * @param   mixed       $value      Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
     * @param   bool|null   $autoload   Whether to load the option when WordPress starts up.
     * @return  bool
     */
    public static function set(string $option, $value, ?bool $autoload = null): bool
    {
        return self::exists($option) ?
            self::update($option, $value, $autoload) : self::add($option, $value, $autoload);
    }
}