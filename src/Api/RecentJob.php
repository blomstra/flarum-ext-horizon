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

use Laminas\Diactoros\Response\JsonResponse;
use Laravel\Horizon\Contracts\JobRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RecentJob implements RequestHandlerInterface
{
    /**
     * @var JobRepository
     */
    private $jobs;

    public function __construct(JobRepository $jobs)
    {
        $this->jobs = $jobs;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $job = (array) $this->jobs->getJobs([$request->getQueryParams()['id']])->map(function ($job) {
            $job->payload = json_decode($job->payload);

            return $job;
        })->first();

        return new JsonResponse($job);
    }
}
