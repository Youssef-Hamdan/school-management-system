<?php

namespace App\Http;

use App\Http\Middleware\HasRole;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetCountry;
use App\Http\Middleware\HasCompletedProfile;
use App\Http\Middleware\HasSubscription;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'jwt.auth' => \App\Http\Middleware\JwtMiddleware::class,
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
    ];
}
