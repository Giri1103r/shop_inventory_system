<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\XSS;
use App\Http\Middleware\CheckLogin;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\LoginCheck;
use App\Http\Middleware\PermisionCheck;
use App\Http\Middleware\IsLogin;
use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\LogRequestInfo;
use App\Http\Middleware\ExtendAccessTokenExpiration;
use App\Http\Middleware\LanguageManager;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\LoginAttempt;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->namespace('App\\Http\\Controllers');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
        $middleware->appendToGroup('language', [
            LanguageManager::class,
        ]);
        $middleware->appendToGroup('xss', [
            XSS::class,
        ]);
        $middleware->appendToGroup('checklogin', [
            CheckLogin::class,
        ]);
        $middleware->appendToGroup('checkadmin', [
            CheckAdmin::class,
        ]);
        $middleware->appendToGroup('logincheck', [
            LoginCheck::class,
        ]);
        $middleware->appendToGroup('permissioncheck', [
            PermisionCheck::class,
        ]);
        $middleware->appendToGroup('islogin', [
            IsLogin::class,
        ]);
        $middleware->appendToGroup('securityheader', [
            AddSecurityHeaders::class,
        ]);
        $middleware->appendToGroup('userlog', [
            LogRequestInfo::class,
        ]);
        $middleware->appendToGroup('loginattempt', [
            LoginAttempt::class,
        ]);
        $middleware->appendToGroup('extendtokenexpiration', [
            ExtendAccessTokenExpiration::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
