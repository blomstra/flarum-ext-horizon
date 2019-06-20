<?php

namespace Bokt\Horizon\Extend;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;

class Horizon implements ExtenderInterface
{
    private $config;
    private $environments = [];

    public function extend(Container $container, Extension $extension = null)
    {
        $repository = $container->make(Repository::class);

        if ($path = $this->config) {
            $config = include $path;
            $repository->set('horizon', $config);
        }

        foreach ($this->environments as $environment => $configuration) {
            $repository->set("horizon.environments.$environment", $configuration);
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

    public function environment(string $environment, array $configuration)
    {
        $this->environments[$environment] = $configuration;

        return $this;
    }
}
