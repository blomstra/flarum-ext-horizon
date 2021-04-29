<?php

namespace Blomstra\Horizon\Api;

use Laravel\Horizon\Contracts\MetricsRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class QueueMetrics implements RequestHandlerInterface
{

    /**
     * @var MetricsRepository
     */
    private $metrics;

    public function __construct(MetricsRepository $metrics)
    {
        $this->metrics = $metrics;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            $this->metrics->measuredQueues()
        );
    }
}
