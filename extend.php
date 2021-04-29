<?php

namespace Blomstra\Horizon;

use Blomstra\Horizon\Api;
use Blomstra\Horizon\Http;
use Blomstra\Redis\Extend\Bindings;
use Flarum\Extend as Flarum;
use FoF\Console\Extend\EnableConsole;
use FoF\Console\Extend\ScheduleCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Horizon\Console as Laravel;

return [
    new Bindings,
    // Horizon provider
    (new Flarum\ServiceProvider)
        ->register(Providers\HorizonServiceProvider::class),
    // Scheduled tasks
    new EnableConsole,
    new ScheduleCommand(function (Schedule $schedule) {
        $schedule->command(Laravel\SnapshotCommand::class)
            ->everyMinute();
    }),
    (new Flarum\Console)
        ->command(Laravel\HorizonCommand::class)
        ->command(Laravel\ListCommand::class)
        ->command(Laravel\PurgeCommand::class)
        ->command(Laravel\PauseCommand::class)
        ->command(Laravel\ContinueCommand::class)
        ->command(Laravel\StatusCommand::class)
        ->command(Laravel\SupervisorCommand::class)
        ->command(Laravel\SupervisorsCommand::class)
        ->command(Laravel\TerminateCommand::class)
        ->command(Laravel\TimeoutCommand::class)
        ->command(Console\WorkCommand::class),
    // Routes
    (new Flarum\Routes('admin'))
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
        ->get('/horizon/api/jobs/recent/{id}', 'horizon.recent-jobs.show', Api\RecentJob::class)
        ->get('/horizon/api/jobs/failed', 'horizon.failed-jobs.index', Api\FailedJobs::class)
        ->get('/horizon/api/jobs/failed/{id}', 'horizon.failed-jobs.show', Api\FailedJob::class)
        ->post('/horizon/api/jobs/retry/{id}', 'horizon.retry-jobs.show', Api\RetryJob::class)
        ->get('/horizon', 'horizon.index', Http\Home::class)
        ->get('/horizon/{view:.*}', 'horizon.index', Http\Home::class),
    // Assets
    new Extend\PublishAssets
];
