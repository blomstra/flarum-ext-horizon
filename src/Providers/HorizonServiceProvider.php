<?php

namespace Bokt\Horizon\Providers;

use Bokt\Horizon\Repositories\RedisJobRepository;
use Flarum\Console\Event\Configuring;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Redis\RedisServiceProvider;
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

        $this->app->booted(function ($app) {
            $this->setupConfiguration($app);

            $this->initRedis();
            $this->registerCommands();
            $this->registerServices();

            $this->registerQueueConnectors();
        });
    }

    protected function initRedis()
    {
        $this->app->register(RedisServiceProvider::class);
        $this->app->alias('redis', Redis::class);
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
        // ..
    }

    protected function setupConfiguration($app)
    {
        $config = include base_path('vendor/laravel/horizon/config/horizon.php');

        Arr::set($config, 'path', 'admin/horizon');
        Arr::set($config, 'use', 'horizon');
        Arr::set($config, 'environments', [
            $app->environment() => [
                'supervisor-1' => [
                    'connection' => 'horizon',
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
        $config = array_merge($config, $flarumConfig['horizon'] ?? [], $existing);

        $repository->set(['horizon' => $config]);

        if (!$repository->has('database.redis')) {
            $repository->set('database.redis', [
                'client'  => 'predis',
                'options' => [
                    'cluster' => 'predis',
                    'prefix'  => '',
                ],
                'horizon' => [
                    'host'     => '127.0.0.1',
                    'password' => null,
                    'port'     => 6379,
                    'database' => 0,
                ],
            ]);
        }

        if (!$repository->has('database.redis.horizon')) {
            $repository->set('database.redis.horizon', [
                'host'     => '127.0.0.1',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ]);
        }

        if (!$repository->has('queue.connections.horizon')) {
            $repository->set('queue.connections.horizon', [
                'driver'      => 'redis',
                'connection'  => 'horizon',
                'queue'       => 'default',
                'retry_after' => 90,
                'block_for'   => null,
            ]);
            $repository->set('queue.default', 'horizon');
        }
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
