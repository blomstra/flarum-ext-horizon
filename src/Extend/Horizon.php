<?php

namespace Bokt\Horizon\Extend;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;

class Horizon implements ExtenderInterface
{
    private $config;

    public function extend(Container $container, Extension $extension = null)
    {
        if ($path = $this->config) {
            $config = include $path;
            $container->make(Repository::class)->set('horizon', $config);
        }
    }

    /**
     * Use a configuration file to configure Horizon.
     *
     * @param string $path
     * @return Horizon
     */
    public function config(string $path)
    {
        $this->config = $path;

        return $this;
    }
}
