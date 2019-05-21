<?php

namespace Bokt\Horizon\Repositories;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Redis\Factory as RedisFactory;

class RedisJobRepository extends \Laravel\Horizon\Repositories\RedisJobRepository
{
    public function __construct(RedisFactory $redis, Repository $config)
    {
        $this->redis = $redis;

        $this->recentJobExpires = $config->get('horizon.trim.recent', 60);
        $this->failedJobExpires = $config->get('horizon.trim.failed', 10080);
        $this->monitoredJobExpires = $config->get('horizon.trim.monitored', 10080);
    }
}
