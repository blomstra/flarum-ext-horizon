<?php

namespace Blomstra\Horizon\Api;

use Illuminate\Bus\BatchRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laravel\Horizon\Contracts\JobRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Batch implements RequestHandlerInterface
{
    public $jobs;
    public $batches;

    public function __construct(JobRepository $jobs, BatchRepository $batches)
    {
        $this->jobs = $jobs;
        $this->batches = $batches;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');
        
        $batch = $this->batches->find($id);

        $failedJobs = $this->jobs->getJobs($batch->failedJobIds);

        return new JsonResponse([
            'batch' => $batch,
            'failedJobs' => $failedJobs,
        ]);
    }
}
