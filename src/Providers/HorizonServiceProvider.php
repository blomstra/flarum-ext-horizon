<?php

namespace Blomstra\Horizon\Providers;

use Blomstra\Horizon\Dispatcher\Notifier;
use Blomstra\Redis\Overrides\RedisManager;
use Flarum\Foundation\Application;
use Flarum\Foundation\Paths;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Notifications\Dispatcher as Notifications;
use Illuminate\Contracts\Redis\Factory;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Support\Arr;
use Laravel\Horizon\Events\LongWaitDetected;
use Laravel\Horizon\HorizonServiceProvider as Provider;
use Laravel\Horizon\RedisQueue;
use Laravel\Horizon\SupervisorCommandString;
use Laravel\Horizon\WorkerCommandString;

class HorizonServiceProvider extends Provider
{
    public function register()
    {
        /** @var Paths $paths */
        $paths = $this->app->make(Paths::class);

        if (!defined('HORIZON_PATH')) {
            define('HORIZON_PATH', realpath($paths->vendor . '/laravel/horizon'));
        }

        SupervisorCommandString::$command = str_replace('artisan', 'flarum', SupervisorCommandString::$command);
        WorkerCommandString::$command     = str_replace('artisan', 'flarum', WorkerCommandString::$command);

        require_once __DIR__ . '/../helpers.php';

        $this->configure();
    }

    public function boot()
    {
        $this->setupConfiguration($this->app);

        $this->registerServices();

        $this->registerQueueConnectors();
        $this->registerNotificationDispatcher();

        parent::boot();
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
        $this->app->extend('flarum.queue.connection', function (\Illuminate\Queue\RedisQueue $queue) {
            /** @var Manager $manager */
            $manager = $this->app->make(Factory::class);
            $queue = new RedisQueue($manager);
            $queue->setContainer($this->app);

            return $queue;
        });

        $this->app->afterResolving(Factory::class, function (RedisManager $manager) {
            if ($config = $manager->getConnectionConfig()) {
                $manager->addConnection('horizon', $config);
            }
        });

        $this->app->extend(CacheFactory::class, function () {
            return new class implements CacheFactory
            {
                public function store($name = null)
                {
                    return app('cache.store');
                }

                public function driver($driver = null)
                {
                    return $this->store($driver);
                }

                public function __call($name, $arguments)
                {
                    return call_user_func_array([$this->store(), $name], $arguments);
                }
            };
        });
    }

    protected function setupConfiguration($container)
    {
        /** @var Paths $paths */
        $paths = $container->make(Paths::class);

        /** @var Application $app */
        $app = $container->make(Application::class);

        $env = $container->make('env');

        $config = include $paths->vendor . '/laravel/horizon/config/horizon.php';

        Arr::set($config, 'env', $env);
        Arr::set($config, 'path', 'admin/horizon');
        Arr::set($config, 'use', 'horizon');
        Arr::set($config, 'environments', [
            $env => [
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
        $repository = $container->make(Repository::class);

        $flarumConfig = $container->make('flarum.config') ?? [];

        // Load existing config items and merge these with a possible key in the config.php.
        // Precedence: existing keys from local extenders, config.php and the default horizon.php.
        $existing = $repository->get('horizon', []);
        $config   = array_merge($config, $flarumConfig['horizon'] ?? [], $existing);

        $repository->set(['horizon' => $config]);
    }

    protected function registerEvents()
    {
        // Remove event listeners for Long wait because it uses the Laravel Notification facade.
        unset($this->events[LongWaitDetected::class]);

        parent::registerEvents();
    }

    public function defineAssetPublishing()
    {

    }

    protected function offerPublishing()
    {

    }

    protected function registerCommands()
    {

    }
}
