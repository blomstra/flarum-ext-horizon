<?php

namespace Blomstra\Horizon\Api;

use Laminas\Diactoros\Response\JsonResponse;
use Laravel\Horizon\Contracts\JobRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SilencedJobs implements RequestHandlerInterface
{
    public $jobs;

    public function __construct(JobRepository $jobs)
    {
        $this->jobs = $jobs;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $jobs = $this->jobs->getSilenced($request->getQueryParams()['starting_at'] ?? -1)->map(function ($job) {
            $job->payload = json_decode($job->payload);

            return $job;
        })->values();

        return new JsonResponse([
            'jobs' => $jobs,
            'total' => $this->jobs->countSilenced(),
        ]);
    }
}
