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

use Laminas\Diactoros\Response\JsonResponse;
use Laravel\Horizon\Contracts\WorkloadRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
