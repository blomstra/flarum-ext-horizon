<?php

namespace Bokt\Horizon\Api;

use Laravel\Horizon\Contracts\JobRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class RecentJobs implements RequestHandlerInterface
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
        $jobs = $this->jobs->getRecent($request->getQueryParams()['starting_at'] ?? -1)->map(function ($job) {
            $job->payload = json_decode($job->payload);

            return $job;
        })->values();

        return new JsonResponse([
            'jobs'  => $jobs,
            'total' => $this->jobs->countRecent(),
        ]);
    }
}
