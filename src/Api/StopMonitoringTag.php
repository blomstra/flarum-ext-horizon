<?php

namespace Bokt\Horizon\Api;

use Illuminate\Contracts\Queue\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;

class StopMonitoringTag implements RequestHandlerInterface
{
    /**
     * @var Factory
     */
    private $queue;

    public function __construct(Factory $queue)
    {
        $this->queue = $queue;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tag = $request->getParsedBody()['tag'];

        $this->queue->dispatch(new \Laravel\Horizon\Jobs\StopMonitoringTag($tag));

        return new EmptyResponse();
    }
}
