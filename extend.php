<?php

namespace Bokt\Horizon;

use Bokt\Horizon\Api;
use Bokt\Horizon\Http;
use Bokt\Redis\Extend\Bindings;
use Flarum\Extend\Routes;
use FoF\Console\Extend\EnableConsole;
use FoF\Console\Extend\ScheduleCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Horizon\Console;

return [
    new Bindings,
    // Horizon provider
    (new Extend\Provider)
        ->add(Providers\HorizonServiceProvider::class),
    // Scheduled tasks
    new EnableConsole,
    new ScheduleCommand(function (Schedule $schedule) {
        $schedule->command(Console\SnapshotCommand::class)
            ->everyMinute();
    }),
    new Extend\Command([
        Console\HorizonCommand::class,
        Console\ListCommand::class,
        Console\PurgeCommand::class,
        Console\PauseCommand::class,
        Console\ContinueCommand::class,
        Console\StatusCommand::class,
        Console\SupervisorCommand::class,
        Console\SupervisorsCommand::class,
        Console\TerminateCommand::class,
        Console\TimeoutCommand::class,
        Console\WorkCommand::class,
    ]),
    // Routes
    (new Routes('admin'))
        ->get('/horizon/api/stats', 'horizon.stats.index', Api\Stats::class)
        ->get('/horizon/api/workload', 'horizon.workload.index', Api\Workload::class)
        ->get('/horizon/api/masters', 'horizon.masters.index', Api\Masters::class)
        ->get('/horizon/api/monitoring', 'horizon.monitoring.index', Api\Monitoring::class)
        ->post('/horizon/api/monitoring', 'horizon.monitoring.store', Api\MonitorTag::class)
        ->get('/horizon/api/monitoring/{tag}', 'horizon.monitoring-tag.paginate', Api\TagMonitoring::class)
        ->delete('/horizon/api/monitoring/{tag}', 'horizon.monitoring-tag.destroy', Api\StopMonitoringTag::class)
        ->get('/horizon/api/metrics/jobs', 'horizon.jobs-metrics.index', Api\Metrics::class)
        ->get('/horizon/api/metrics/jobs/{id}', 'horizon.jobs-metrics.show', Api\JobMetrics::class)
        ->get('/horizon/api/metrics/queues', 'horizon.queues-metrics.index', Api\QueueMetrics::class)
        ->get('/horizon/api/metrics/queues/{id}', 'horizon.queues-metrics.show', Api\QueueJobMetrics::class)
        ->get('/horizon/api/jobs/recent', 'horizon.recent-jobs.index', Api\RecentJobs::class)
        ->get('/horizon/api/jobs/failed', 'horizon.failed-jobs.index', Api\FailedJobs::class)
        ->get('/horizon/api/jobs/failed/{id}', 'horizon.failed-jobs.show', Api\FailedJob::class)
        ->post('/horizon/api/jobs/retry/{id}', 'horizon.retry-jobs.show', Api\RetryJob::class)
        ->get('/horizon/{view:.*}', 'horizon.index', Http\Home::class),
    // Assets
    new Extend\PublishAssets(
        base_path('vendor/laravel/horizon/public'),
        public_path('assets/horizon')
    )
];
