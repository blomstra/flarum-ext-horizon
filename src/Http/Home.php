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
     * @var Cloud
     */
    private $assetsDir;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Factory $view, FilesystemFactory $filesystem, Config $config)
    {
        $this->view = $view;
        $this->assetsDir = $filesystem->disk('flarum-assets');
        $this->config = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->view->make('horizon::layout', [
            'assetsAreCurrent'             => !$this->config->inDebugMode(),
            'cssFileLight'                 => 'app.css',
            'cssFileDark'                  => 'app-dark.css',
            'horizonScriptVariables'       => Horizon::scriptVariables(),
            'isDownForMaintenance'         => $this->config->inMaintenanceMode(),
            'assetsUrl'                    => $this->assetsDir->url(''),
        ])->render());
    }
}
