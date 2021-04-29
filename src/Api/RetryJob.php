<?php

namespace Blomstra\Horizon\Api;

use Illuminate\Contracts\Queue\Factory;
use Laravel\Horizon\Jobs\RetryFailedJob;
use Laravel\Horizon\RedisQueue;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\EmptyResponse;

class RetryJob implements RequestHandlerInterface
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
        $id = $request->getParsedBody()['id'];

        $this->queue->push(new RetryFailedJob($id));

        return new EmptyResponse();
    }
}
