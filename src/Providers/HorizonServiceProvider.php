<?php

namespace Bokt\Horizon\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Laravel\Horizon\HorizonServiceProvider as Provider;

class HorizonServiceProvider extends Provider
{
    public function register()
    {

    }

    protected function registerRoutes()
    {
        // .. via extend.php
    }

    protected function registerResources()
    {
        /** @var Factory $view */
        $view = $this->app->make(Factory::class);

        $view->addNamespace('horizon', base_path('vendor/laravel/horizon/resources/views'));
    }

    protected function configure()
    {
        $config = include base_path('vendor/laravel/horizon/config/horizon.php');

        /** @var Repository $repository */
        $repository = $this->app->make(Repository::class);
        $repository->set(['horizon' => $config]);
    }
}
