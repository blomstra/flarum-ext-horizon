<?php

namespace Blomstra\Horizon\Api;

use Laravel\Horizon\Contracts\WorkloadRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class Workload implements RequestHandlerInterface
{

    private $workload;

    public function __construct(WorkloadRepository $workload)
    {
        $this->workload = $workload;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(collect($this->workload->get())->sortBy('name')->values()->toArray());
    }
}
