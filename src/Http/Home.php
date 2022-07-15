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

namespace Blomstra\Horizon\Http;

use Flarum\Frontend\Frontend;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\View\Factory;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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

    /**
     * @var Filesystem
     */
    private $assetsDir;

    public function __construct(Factory $view, Frontend $frontend, FilesystemFactory $filesystem)
    {
        $this->view = $view;
        $this->frontend = $frontend;
        $this->assetsDir = $filesystem->disk('flarum-assets');
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->view->make('horizon::layout', [
            'cssFile'                => 'app.css',
            'horizonScriptVariables' => [
                'path' => 'admin/horizon',
            ],
            'assetsUrl'                    => $this->assetsDir->url('/'),
        ])->render());
    }
}
