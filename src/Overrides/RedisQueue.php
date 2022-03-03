<?php

namespace Blomstra\Horizon\Overrides;

use Laravel\Horizon\RedisQueue as HorizonBaseQueue;

class RedisQueue extends HorizonBaseQueue
{
    /**
     * Push a new job onto the queue.
     *
     * @param  object|string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        if ($job->queue && !$queue) {
            $queue = $job->queue;
        }
        
        return parent::push($job, $data, $queue);
    }
}
