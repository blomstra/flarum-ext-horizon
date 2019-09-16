<?php

namespace Bokt\Horizon\Providers;

use Bokt\Horizon\Dispatcher\Notifier;
use Bokt\Horizon\Repositories\RedisJobRepository;
use Bokt\Redis\Manager;
use Flarum\Console\Event\Configuring;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Notifications\Dispatcher as Notifications;
use Illuminate\Contracts\Redis\Factory;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Support\Arr;
use Laravel\Horizon\HorizonServiceProvider as Provider;
use Laravel\Horizon\Console;
use Laravel\Horizon\SupervisorCommandString;
use Laravel\Horizon\WorkerCommandString;

class HorizonServiceProvider extends Provider
{
    public function register()
    {
        if (!defined('HORIZON_PATH')) {
            define('HORIZON_PATH', realpath(base_path('vendor/laravel/horizon')));
        }

        SupervisorCommandString::$command = str_replace('artisan', 'flarum', SupervisorCommandString::$command);
        WorkerCommandString::$command     = str_replace('artisan', 'flarum', WorkerCommandString::$command);

        require_once __DIR__ . '/../helpers.php';

        $this->configure();

        $this->app->booted(function ($app) {
            $this->setupConfiguration($app);

            $this->registerCommands();
            $this->registerServices();

            $this->registerQueueConnectors();
            $this->registerNotificationDispatcher();
        });
    }

    protected function registerNotificationDispatcher()
    {
        if (!$this->app->bound(Notifications::class)) {
            $this->app->singleton(Notifications::class, function () {
                return new Notifier;
            });
        }
    }

    protected function registerRoutes()
    {
        // .. via extend.php
    }

    protected function registerResources()
    {
        /** @var Factory $view */
        $view = $this->app->make(View::class);

        $view->addNamespace('horizon', __DIR__ . '/../../resources/views');
    }

    protected function configure()
    {
        $this->app->afterResolving(Factory::class, function (Manager $manager) {
            $manager->addConnection('horizon', $manager->getConnectionConfig());
        });
    }

    protected function setupConfiguration($app)
    {
        $config = include base_path('vendor/laravel/horizon/config/horizon.php');

        Arr::set($config, 'path', 'admin/horizon');
//        Arr::set($config, 'use', 'horizon');
        Arr::set($config, 'environments', [
            $app->environment() => [
                'supervisor-1' => [
                    'connection' => 'redis',
                    'queue'      => ['default'],
                    'balance'    => 'balanced',
                    'processes'  => 4,
                    'tries'      => 3,
                ]
            ]
        ]);

        /** @var Repository $repository */
        $repository = $app->make(Repository::class);

        $flarumConfig = $app->make('flarum.config') ?? [];

        // Load existing config items and merge these with a possible key in the config.php.
        // Precedence: existing keys from local extenders, config.php and the default horizon.php.
        $existing = $repository->get('horizon', []);
        $config   = array_merge($config, $flarumConfig['horizon'] ?? [], $existing);

        $repository->set(['horizon' => $config]);
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
                         Console\SnapshotCommand::class
                     ] as $command) {
                $event->addCommand($command);
            }
        });
    }
}
