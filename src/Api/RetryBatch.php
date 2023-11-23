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

namespace Blomstra\Horizon\Api;

use Illuminate\Bus\BatchRepository;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\EmptyResponse;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Jobs\RetryFailedJob;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RetryBatch implements RequestHandlerInterface
{
    public $jobs;
    public $batches;
    public $queue;

    public function __construct(JobRepository $jobs, BatchRepository $batches, Queue $queue)
    {
        $this->jobs = $jobs;
        $this->batches = $batches;
        $this->queue = $queue;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = Arr::get($request->getQueryParams(), 'id');

        $batch = $this->batches->find($id);

        $this->jobs->getJobs($batch->failedJobIds)
            ->reject(function ($job) {
                $payload = json_decode($job->payload);

                return isset($payload->retry_of);
            })->each(function ($job) {
                $this->queue->push(new RetryFailedJob($job->id));
            });

        return new EmptyResponse();
    }
}
