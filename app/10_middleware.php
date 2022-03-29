<?php

namespace App;


use Brace\Body\BodyMiddleware;
use Brace\Core\AppLoader;
use Brace\Core\Base\ExceptionHandlerMiddleware;
use Brace\Core\Base\JsonReturnFormatter;
use Brace\Core\Base\NotFoundMiddleware;
use Brace\Core\BraceApp;
use Brace\CORS\CorsMiddleware;
use Brace\Router\RouterDispatchMiddleware;
use Brace\Router\RouterEvalMiddleware;
use Brace\Session\SessionMiddleware;
use Brace\Session\Storages\CookieSessionStorage;
use Micx\Whois\Config\Config;


AppLoader::extend(function (BraceApp $app) {

    $app->setPipe([
        new BodyMiddleware(),
        new CorsMiddleware([], function (string $subscriptionId, Config $config, string $origin) {
            return in_array($origin, $config->allow_origins);
        }),

        new ExceptionHandlerMiddleware(),
        new RouterEvalMiddleware(),
        new RouterDispatchMiddleware([
            new JsonReturnFormatter($app)
        ]),
        new NotFoundMiddleware()
    ]);
});
