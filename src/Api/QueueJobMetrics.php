<?php

namespace Bokt\Horizon\Api;

use Laravel\Horizon\Contracts\MetricsRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class QueueJobMetrics implements RequestHandlerInterface
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
        $slug = $request->getQueryParams()['id'];

        return new JsonResponse(
            collect($this->metrics->snapshotsForQueue($slug))->map(function ($record) {
                $record->runtime = round($record->runtime / 1000, 3);
                $record->throughput = (int) $record->throughput;

                return $record;
            })
        );
    }
}
