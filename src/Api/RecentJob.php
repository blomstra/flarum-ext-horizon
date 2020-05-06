<?php

namespace Bokt\Horizon\Api;

use Laravel\Horizon\Contracts\JobRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

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
