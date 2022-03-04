<?php

/*
 * This file is part of blomstra/horizon.
 *
 * Copyright (c) Bokt.
 * Copyright (c) Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Horizon\Overrides;

use Laravel\Horizon\RedisQueue as HorizonBaseQueue;

class RedisQueue extends HorizonBaseQueue
{
    /**
     * Push a new job onto the queue.
     *
     * @param object|string $job
     * @param mixed         $data
     * @param string|null   $queue
     *
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
