<?php

namespace Blomstra\Horizon\Http;

use Flarum\Frontend\Frontend;
use Illuminate\Contracts\View\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class Home implements RequestHandlerInterface
{
    /**
     * @var Factory
     */
    private $view;
    /**
     * @var Frontend
     */
    private $frontend;

    public function __construct(Factory $view, Frontend $frontend)
    {
        $this->view = $view;
        $this->frontend = $frontend;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->view->make('horizon::layout', [
            'cssFile' => 'app.css',
            'horizonScriptVariables' => [
                'path' => 'admin/horizon'
            ],
        ])->render());
    }
}
