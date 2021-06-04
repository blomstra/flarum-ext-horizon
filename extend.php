<?php

namespace Blomstra\Horizon;

use Blomstra\Horizon\Api;
use Blomstra\Horizon\Http;
use Blomstra\Redis\Extend\Bindings;
use Flarum\Extend as Flarum;
use Illuminate\Console\Scheduling\Event;
use Laravel\Horizon\Console as Laravel;

return [
    new Bindings,
    (new Flarum\ServiceProvider)
        ->register(Providers\HorizonServiceProvider::class),

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
        ->command(Console\WorkCommand::class)
        ->schedule(Laravel\SnapshotCommand::class, function (Event $schedule) {
            $schedule->everyMinute();
        }),
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
        ->get('/horizon/{view:.*}', 'horizon.index.view', Http\Home::class),
    // Assets
    new Extend\PublishAssets
];
