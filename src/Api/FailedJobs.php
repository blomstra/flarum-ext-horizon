<?php

namespace Bokt\Horizon\Api;

use Illuminate\Support\Arr;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\TagRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class FailedJobs implements RequestHandlerInterface
{
    /**
     * @var JobRepository
     */
    private $jobs;
    /**
     * @var TagRepository
     */
    private $tags;

    public function __construct(JobRepository $jobs, TagRepository $tags)
    {
        $this->jobs = $jobs;
        $this->tags = $tags;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tag = $request->getQueryParams()['tag'] ?? null;

        $jobs = ! $tag
            ? $this->paginate($request)
            : $this->paginateByTag($request, $tag);

        $total = $tag
            ? $this->tags->count('failed:'.$tag)
            : $this->jobs->countFailed();

        return new JsonResponse([
            'jobs'  => $jobs,
            'total' => $total,
        ]);
    }

    protected function paginate(ServerRequestInterface $request)
    {
        return $this->jobs->getFailed(Arr::get($request->getQueryParams(), 'starting_at', -1))->map(function ($job) {
            return $this->decode($job);
        });
    }

    protected function paginateByTag(ServerRequestInterface $request, $tag)
    {
        $jobIds = $this->tags->paginate(
            'failed:'.$tag, (Arr::get($request->getQueryParams(), 'starting_at', -1)) + 1, 50
        );

        $startingAt = Arr::get($request->getQueryParams(), 'starting_at', 0);

        return $this->jobs->getJobs($jobIds, $startingAt)->map(function ($job) {
            return $this->decode($job);
        });
    }

    protected function decode($job)
    {
        $job->payload = json_decode($job->payload);

        $job->retried_by = collect(json_decode($job->retried_by))
            ->sortByDesc('retried_at')->values();

        return $job;
    }
}
