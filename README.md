# Horizon, the queue dashboard

This extension adds full integration for [Laravel Horizon](https://laravel.com/docs/8.x/horizon).

Which includes:

- a dashboard at (yoursite.com/admin/horizon)
- scalable redis workers with balancing strategies
- multiple scalable redis worker servers (untested)
- and much more.

![](https://laravel.com/img/docs/horizon-example.png)

Laravel Horizon runs only using a redis connection. As such you 
**have to configure** [blomstra/redis](https://discuss.flarum.org/d/21873). If you don't you will
see errors pop up.


### Installation
Install manually with composer:

```sh
composer require blomstra/horizon:*
```

### Set up

Enable the extension from your admin area and then run `php flarum horizon`. This will only run as long as your
process is active, so make sure to set it up using supervisor or something similar, see the [Horizon Documentation](https://laravel.com/docs/8.x/horizon#deploying-horizon)
for instructions.

### Configure

By default this extension will set up a default queue connection called
`horizon` using redis. You can override the full horizon config using
an extender in your local `extend.php` in the root of your flarum
installation:

```php
<?php

return [
    (new Blomstra\Horizon\Extend\Horizon)->config(
        './your-horizon-config.php'
    )
];
```

### Links

- [Packagist](https://packagist.org/packages/blomstra/horizon)
- [GitHub](https://github.com/blomstra/flarum-ext-horizon)

---

- Blomstra provides managed Flarum hosting.
- https://blomstra.net
