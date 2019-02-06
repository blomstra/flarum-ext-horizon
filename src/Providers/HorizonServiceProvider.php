<?php

namespace Bokt\Horizon\Providers;

use Flarum\Console\Event\Configuring;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonServiceProvider as Provider;
use Laravel\Horizon\Console;

class HorizonServiceProvider extends Provider
{
    public function register()
    {
        if (! defined('HORIZON_PATH')) {
            define('HORIZON_PATH', realpath(base_path('vendor/laravel/horizon')));
        }
        $this->configure();
        $this->registerCommands();
        $this->registerServices();
        $this->registerQueueConnectors();
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

        $flarumConfig = $this->app->make('flarum.config');

        $config = array_merge($config, $flarumConfig['queue'] ?? []);

        $repository->set(['horizon' => $config]);

//        Horizon::use($config['use']);
    }

    protected function registerCommands()
    {
        /** @var Dispatcher $events */
        $events = $this->app->make('events');

        $events->listen(Configuring::class, function (Configuring $event) {
            foreach ([
                Console\HorizonCommand::class,
                Console\ListCommand::class,
                Console\PurgeCommand::class,
                Console\PauseCommand::class,
                Console\ContinueCommand::class,
                Console\SupervisorCommand::class,
                Console\SupervisorsCommand::class,
                Console\TerminateCommand::class,
                Console\TimeoutCommand::class,
                Console\WorkCommand::class,
                Console\SnapshotCommand::class] as $command) {
                $event->addCommand($command);
            }
        });
    }
}
