<?php

namespace Bokt\Horizon;

use Flarum\Extend\Frontend;

return [
    (new Frontend('admin'))
        ->route('horizon/api/stats')
        ->route('horizon/api/workload')
        ->route('horizon/api/masters')
        ->route('horizon/api/monitoring')
        ->route('horizon/api/monitoring/{tag}')
        ->route('horizon/api/metrics/jobs')
        ->route('horizon/api/metrics/jobs/{id}')
        ->route('horizon/api/metrics/queues')
        ->route('horizon/api/metrics/queues/{id}')
        ->route('horizon/api/jobs/recent')
        ->route('horizon/api/jobs/failed')
        ->route('horizon/api/jobs/failed/{id}')
        ->route('.+', 'horizon.home')
];
