<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
'is_admin' => \App\Http\Middleware\IsAdmin::class,
        // ✅ Custom middleware
        'is_admin' => \App\Http\Middleware\IsAdmin::class,
    ];


    

protected $middlewareGroups = [
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'api' => [
        EnsureFrontendRequestsAreStateful::class,
        'throttle:api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
];
// protected $routeMiddleware = [
    
// ];
   
}
