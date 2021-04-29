<?php

namespace Blomstra\Horizon\Api;

use Illuminate\Contracts\Queue\Factory;
use Laravel\Horizon\Jobs\MonitorTag as MonitorTagJob;
use Laravel\Horizon\RedisQueue;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\EmptyResponse;

class MonitorTag implements RequestHandlerInterface
{
    /**
     * @var Factory|RedisQueue
     */
    private $queue;

    public function __construct(Factory $queue)
    {
        $this->queue = $queue;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tag = $request->getParsedBody()['tag'];

        $this->queue->push(new MonitorTagJob($tag));

        return new EmptyResponse();
    }
}
