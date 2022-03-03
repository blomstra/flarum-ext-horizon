<?php

namespace Blomstra\Horizon\Api;

use Illuminate\Contracts\Config\Repository;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\SupervisorRepository;
use Laravel\Horizon\WaitTimeCalculator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class Stats implements RequestHandlerInterface
{
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'jobsPerMinute' => resolve(MetricsRepository::class)->jobsProcessedPerMinute(),
            'processes' => $this->totalProcessCount(),
            'queueWithMaxRuntime' => resolve(MetricsRepository::class)->queueWithMaximumRuntime(),
            'queueWithMaxThroughput' => resolve(MetricsRepository::class)->queueWithMaximumThroughput(),
            'recentlyFailed' => resolve(JobRepository::class)->countRecentlyFailed(),
            'recentJobs' => resolve(JobRepository::class)->countRecent(),
            'status' => $this->currentStatus(),
            'wait' => collect(resolve(WaitTimeCalculator::class)->calculate())->take(1),
            'periods' => [
                'recentJobs' => $this->config->get('horizon.trim.recent'),
                'recentlyFailed' => $this->config->get('horizon.trim.failed'),
            ],
        ]);
    }

    protected function totalProcessCount()
    {
        $supervisors = resolve(SupervisorRepository::class)->all();

        return collect($supervisors)->reduce(function ($carry, $supervisor) {
            return $carry + collect($supervisor->processes)->sum();
        }, 0);
    }
    protected function currentStatus()
    {
        if (! $masters = resolve(MasterSupervisorRepository::class)->all()) {
            return 'inactive';
        }

        return collect($masters)->contains(function ($master) {
            return $master->status === 'paused';
        }) ? 'paused' : 'running';
    }
}
