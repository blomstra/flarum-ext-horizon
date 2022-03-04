<?php

/*
 * This file is part of blomstra/horizon.
 *
 * Copyright (c) Bokt.
 * Copyright (c) Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Horizon\Extend;

use Flarum\Extend\ExtenderInterface;
use Flarum\Extend\LifecycleInterface;
use Flarum\Extension\Extension;
use Flarum\Foundation\Paths;
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

    public function __construct()
    {
        /** @var Paths $paths */
        $paths = resolve(Paths::class);

        $this->from = $paths->vendor.'/laravel/horizon/public';
        $this->to = $paths->public.'/assets/horizon';
    }

    public function onEnable(Container $container, Extension $extension)
    {
        if ($extension->name === 'blomstra/horizon') {
            $container->make('files')->copyDirectory(
                $this->from,
                $this->to
            );
        }
    }

    public function onDisable(Container $container, Extension $extension)
    {
        if ($extension->name === 'blomstra/horizon') {
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
