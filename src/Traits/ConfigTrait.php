<?php

namespace Irmmr\WpNotifBell\Traits;

// If this file is called directly, abort.
defined('WPINC') || die;

/**
 * Trait Config
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Traits
 */
trait ConfigTrait
{
    /**
     * !! init
     * configs default
     * 
     * @since   0.9.0
     * @var     array   $configs_default
     */
    protected array $configs_default;

    /**
     * configs handler
     * 
     * @since   0.9.0
     * @var     array   $configs
     */
    protected array $configs = [];

    /**
     * apply configs
     * 
     * @since   0.9.0
     * @param   array   $config
     * @return  self
     */
    public function config(array $config): self
    {
        $this->configs = array_merge($this->configs, $config);
        $this->configs = array_merge($this->configs_default, $this->configs);

        return $this;
    }

    /**
     * check for config existing
     * 
     * @since   0.9.0
     * @param   string  $name
     * @return  bool
     */
    protected function has_config(string $name): bool
    {
        return array_key_exists($name, $this->configs);
    }

    /**
     * get a config data
     * 
     * @since   0.9.0
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    protected function get_config(string $name, $default = null)
    {
        if (!$this->has_config($name)) {
            return $this->configs_default[$name] ?? $default;
        }

        return $this->configs[$name];
    }
}