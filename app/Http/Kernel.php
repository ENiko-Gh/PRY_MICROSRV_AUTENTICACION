<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
// Importamos la clase de nuestro middleware de rol
use App\Http\Middleware\CheckUserRole;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * Se han quitado los namespaces redundantes de App\Http\Middleware
     * para los archivos generados por Laravel, resolviendo los errores de "Undefined type".
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Middleware de la aplicación (Sin el namespace completo App\Http\Middleware)
        \App\Http\Middleware\TrustProxies::class,
        // Middlewares de framework (Laravel Foundation)
        \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        // Middlewares de la aplicación (Sin el namespace completo App\Http\Middleware)
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // Middlewares del grupo 'web' (Quitamos los namespaces problemáticos de App\Http\Middleware)
            \App\Http\Middleware\EncryptCookies::class, // Corregido
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class, // Corregido
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        // Corregido: La clase 'RedirectIfAuthenticated' está en App\Http\Middleware
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Alias de Middleware de Rol (que fallaba en el test)
        'role' => CheckUserRole::class,
    ];
}
