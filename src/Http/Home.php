<?php

namespace Bokt\Horizon\Http;

use Flarum\Frontend\Frontend;
use Illuminate\Contracts\View\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\HtmlResponse;

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

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->view->make('horizon::app')->render());
    }
}
