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
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Factory;

class PublishAssets implements LifecycleInterface, ExtenderInterface
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var Cloud
     */
    private $assetsDisk;

    public function __construct()
    {
        $paths = resolve(Paths::class);
        $factory = resolve(Factory::class);

        $this->from = $paths->vendor.'/laravel/horizon/public';
        $this->assetsDisk = $factory->disk('flarum-assets');
    }

    public function onEnable(Container $container, Extension $extension)
    {
        if ($extension->name === 'blomstra/horizon') {

            /** @var \Illuminate\Filesystem\Filesystem $localFilesystem */
            $localFilesystem = $container->make('files');

            foreach ($localFilesystem->allFiles($this->from) as $file) {
                /** @var \Symfony\Component\Finder\SplFileInfo $file */
                $this->assetsDisk->put('horizon/'.$file->getRelativePathname(), $file->getContents());
            }
        }
    }

    public function onDisable(Container $container, Extension $extension)
    {
        if ($extension->name === 'blomstra/horizon') {
            $this->assetsDisk->deleteDirectory('horizon');
        }
    }

    public function extend(Container $container, Extension $extension = null)
    {
        // TODO: Implement extend() method.
    }
}
