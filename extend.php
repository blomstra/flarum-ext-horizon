<?php

namespace Bokt\Horizon;

use Flarum\Extend\Frontend;

return [
    (new Frontend('admin'))
        ->route('horizon/api/stats', 'horizon.api.stats')
        ->route('horizon/api/workload', 'horizon.api.workload')
        ->route('horizon/api/masters', 'horizon.api.masters')
        ->route('horizon/api/monitoring', 'horizon.api.monitoring')
        ->route('horizon/api/monitoring/{tag}', 'horizon.api.monitoring.tag')
        ->route('horizon/api/metrics/jobs', 'horizon.api.metrics.jobs')
        ->route('horizon/api/metrics/jobs/{id}', 'horizon.api.metrics.job')
        ->route('horizon/api/metrics/queues', 'horizon.api.metrics.queues')
        ->route('horizon/api/metrics/queues/{id}', 'horizon.api.metrics.queue')
        ->route('horizon/api/jobs/recent', 'horizon.api.jobs.recent')
        ->route('horizon/api/jobs/failed', 'horizon.api.jobs.failed')
        ->route('horizon/api/jobs/failed/{id}', 'horizon.api.jobs.fail')
        ->route('horizon/.+', 'horizon.home')
];
