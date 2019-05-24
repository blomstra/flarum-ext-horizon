<?php

namespace Bokt\Horizon\Extend;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extend\LifecycleInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

class PublishAssets implements LifecycleInterface, ExtenderInterface
{
    /**
     * @var string
     */
    private $from;
    /**
     * @var string
     */
    private $to;

    public function __construct(string $from, string $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function onEnable(Container $container, Extension $extension)
    {
        if ($extension->name === 'bokt/flarum-horizon') {
            $container->make('files')->copyDirectory(
                $this->from,
                $this->to
            );
        }
    }

    public function onDisable(Container $container, Extension $extension)
    {
        if ($extension->name === 'bokt/flarum-horizon') {
            $container->make('files')->deleteDirectory(
                $this->to
            );
        }
    }

    public function extend(Container $container, Extension $extension = null)
    {
        // TODO: Implement extend() method.
    }
}