<?php

/*
 * This file is part of blomstra/horizon.
 *
 * Copyright (c) Bokt.
 * Copyright (c) Blomstra Ltd.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blomstra\Horizon\Api;

use Illuminate\Contracts\Queue\Factory;
use Laminas\Diactoros\Response\EmptyResponse;
use Laravel\Horizon\Jobs\StopMonitoringTag as StopMonitoringTagJob;
use Laravel\Horizon\RedisQueue;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class StopMonitoringTag implements RequestHandlerInterface
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

        $this->queue->push(new StopMonitoringTagJob($tag));

        return new EmptyResponse();
    }
}
