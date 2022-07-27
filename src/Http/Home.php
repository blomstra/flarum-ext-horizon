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

use Flarum\Foundation\Config;
use Flarum\Frontend\Frontend;
use Flarum\Http\UrlGenerator;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\View\Factory;
use Laminas\Diactoros\Response\HtmlResponse;
use Laravel\Horizon\Horizon;
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
     * @var Cloud
     */
    private $assetsDir;

    /**
     * @var UrlGenerator
     */
    private $url;

    /** @var Config */
    private $config;

    public function __construct(Factory $view, Frontend $frontend, FilesystemFactory $filesystem, UrlGenerator $url, Config $config)
    {
        $this->view = $view;
        $this->frontend = $frontend;
        $this->assetsDir = $filesystem->disk('flarum-assets');
        $this->url = $url;
        $this->config = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->view->make('horizon::layout', [
            'assetsAreCurrent'             => !$this->config->inDebugMode(),
            'cssFile'                      => 'app.css', // TODO: support fof/nightmode
            'horizonScriptVariables'       => Horizon::scriptVariables(),
            'isDownForMaintenance'         => $this->config->inMaintenanceMode(),
            'assetsUrl'                    => $this->assetsDir->url(''),
        ])->render());
    }
}
