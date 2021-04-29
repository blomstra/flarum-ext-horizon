<?php

namespace Blomstra\Horizon\Api;

use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\SupervisorRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class Masters implements RequestHandlerInterface
{
    /**
     * @var MasterSupervisorRepository
     */
    private $masters;
    /**
     * @var SupervisorRepository
     */
    private $supervisors;

    public function __construct(MasterSupervisorRepository $masters, SupervisorRepository $supervisors)
    {
        $this->masters = $masters;
        $this->supervisors = $supervisors;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse($this->index());
    }

    protected function index()
    {
        $masters = collect($this->masters->all())->keyBy('name')->sortBy('name');

        $supervisors = collect($this->supervisors->all())->sortBy('name')->groupBy('master');

        return $masters->each(function ($master, $name) use ($supervisors) {
            $master->supervisors = $supervisors->get($name);
        });
    }
}
