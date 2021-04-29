<?php

namespace Blomstra\Horizon\Console;

use Flarum\Foundation\Config;

class WorkCommand extends \Laravel\Horizon\Console\WorkCommand
{
    protected function downForMaintenance()
    {
        if ($this->option('force')) {
            return false;
        }

        /** @var Config $config */
        $config = $this->laravel->make(Config::class);

        return $config->inMaintenanceMode();
    }
}
