<?php

namespace Bokt\Horizon;

use Bokt\Horizon\Http;
use Bokt\Queue\Extend\EnableQueues;
use Flarum\Extend\Routes;

return [
    new EnableQueues,
    (new Extend\Provider)
        ->add(Providers\HorizonServiceProvider::class),
    (new Routes('admin'))
//        ->get('horizon/api/stats', 'horizon.stats.index', )
//        ->get('horizon/api/workload', 'horizon.workload.index')
//        ->get('horizon/api/masters', 'horizon.masters.index')
//        ->get('horizon/api/monitoring', 'horizon.monitoring.index')
//        ->post('horizon/api/monitoring', 'horizon.monitoring.store')
//        ->get('horizon/api/monitoring/{tag}', 'horizon.monitoring-tag.paginate')
//        ->delete('horizon/api/monitoring/{tag}', 'horizon.monitoring-tag.destroy')
//        ->get('horizon/api/metrics/jobs', 'horizon.jobs-metrics.index')
//        ->get('horizon/api/metrics/jobs/{id}', 'horizon.jobs-metrics.show')
//        ->get('horizon/api/metrics/queues', 'horizon.queues-metrics.index')
//        ->get('horizon/api/metrics/queues/{id}', 'horizon.queues-metrics.show')
//        ->get('horizon/api/jobs/recent', 'horizon.recent-jobs.index')
//        ->get('horizon/api/jobs/failed', 'horizon.failed-jobs.index')
//        ->get('horizon/api/jobs/failed/{id}', 'horizon.failed-jobs.show')
//        ->post('horizon/api/jobs/retry/{id}', 'horizon.retry-jobs.show')
        ->get('/horizon/{view:.*}', 'horizon.index', Http\Home::class)
];
