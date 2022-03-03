<?php

use Illuminate\Contracts\Config\Repository;

if (! function_exists('config')) {
    function config(string $key, $default = null) {
        /** @var Repository $config */
        $config = resolve(Repository::class);

        return $config->get($key, $default);
    }
}
