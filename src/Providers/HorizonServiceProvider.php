<?php

namespace Bokt\Horizon\Providers;

use Bokt\Horizon\Repositories\RedisJobRepository;
use Flarum\Console\Event\Configuring;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Support\Arr;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\HorizonServiceProvider as Provider;
use Laravel\Horizon\Console;

class HorizonServiceProvider extends Provider
{
    public function register()
    {
        if (! defined('HORIZON_PATH')) {
            define('HORIZON_PATH', realpath(base_path('vendor/laravel/horizon')));
        }

        require_once __DIR__ . '/../helpers.php';

        $this->configure();

        $this->initRedis();
        $this->registerCommands();
        $this->registerServices();

        $this->registerQueueConnectors();
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
        $config = include base_path('vendor/laravel/horizon/config/horizon.php');
        Arr::set($config, 'path', 'admin/horizon');
        Arr::set($config, 'use', 'horizon');
        Arr::set($config, 'env', 'production');
        Arr::set($config, 'environments', [
            'production' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'balanced',
                'processes' => 4,
                'tries' => 3,
            ]
        ]);

        /** @var Repository $repository */
        $repository = $this->app->make(Repository::class);

        $flarumConfig = $this->app->make('flarum.config');

        // Merge default config with the horizon key in config.php.
        $config = array_merge($config, $flarumConfig['horizon'] ?? []);

        $repository->set(['horizon' => $config]);

        if (! $repository->has('database.redis')) {
            $repository->set('database.redis', [
                'client' => 'predis',
                'options' => [
                    'cluster' => 'predis',
                    'prefix' => 'flarum',
                ],
                'horizon' => [
                    'host' => '127.0.0.1',
                    'password' =>  null,
                    'port' => 6379,
                    'database' => 0,
                ],
            ]);
        }

        if (! $repository->has('database.redis.horizon')) {
            $repository->set('database.redis.horizon', [
                'host' => '127.0.0.1',
                'password' =>  null,
                'port' => 6379,
                'database' => 0,
            ]);
        }

        if (! $repository->has('queue.connections.horizon')) {
            $repository->set('queue.connections.horizon', [
                'driver' => 'redis',
                'connection' => 'horizon',
                'queue' => 'default',
                'retry_after' => 90,
                'block_for' => null,
            ]);
        }

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
