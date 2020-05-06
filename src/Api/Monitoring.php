<?php

namespace Bokt\Horizon\Api;

use Laravel\Horizon\Contracts\TagRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class Monitoring implements RequestHandlerInterface
{
    /**
     * @var TagRepository
     */
    private $tags;

    public function __construct(TagRepository $tags)
    {
        $this->tags = $tags;
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            collect($this->tags->monitoring())->map(function ($tag) {
                return [
                    'tag' => $tag,
                    'count' => $this->tags->count($tag) + $this->tags->count('failed:'.$tag),
                ];
            })->sortBy('tag')->values()
        );
    }
}
