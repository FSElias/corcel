<?php

namespace Corcel\Services;

use Corcel\Model\Option;
use Illuminate\Support\Arr;

/**
 * Class RoleManager
 *
 * @package Corcel\Services
 * @author Junior Grossi <juniorgro@gmail.com>
 * @todo Update, delete and list roles
 */
class RoleManager
{
    /**
     * @var string
     */
    protected $optionKey = 'wp_user_roles';

    /**
     * @var array
     */
    protected $option = [];

    /**
     * @var array
     */
    protected $capabilities = [];

    /**
     * Create a new RoleManager instance
     */
    public function __construct()
    {
        $this->option = Option::get($this->optionKey); // TODO set this to the Option model
    }

    /**
     * @param string $role
     * @return array
     */
    public function get($role)
    {
        return Arr::get($this->option, $role);
    }

    /**
     * @param string $role
     * @return $this
     */
    public function from($role)
    {
        $this->capabilities = Arr::get(
            $this->get($role), 'capabilities'
        );

        return $this;
    }

    /**
     * @param string $name
     * @param array $capabilities
     * @return array
     */
    public function create($name, array $capabilities)
    {
        $key = str_slug($name, '_');

        $this->option[$key] = $role = [
            'name' => $name,
            'capabilities' => array_merge($this->capabilities, $capabilities),
        ];

        $option = Option::query()
            ->where(['option_name' => $this->optionKey])
            ->first();

        $option->option_value = serialize($this->option);
        $result = $option->save();

        return $result ? $role : null;
    }
}
