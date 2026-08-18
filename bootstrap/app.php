<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // Trust the reverse proxy in front of us (ngrok in dev, or a real
        // load balancer/CDN in production) so Laravel reads the original
        // https:// scheme from X-Forwarded-Proto instead of assuming plain
        // http — otherwise asset()/Vite/url() links get generated as
        // http://, which browsers block as mixed content on an https page.
        $middleware->trustProxies(at: '*');

        // Server-to-server payment gateway callbacks carry no Laravel
        // session/CSRF token — they're authenticated by signature instead.
        $middleware->validateCsrfTokens(except: [
            'thanh-toan/momo/ipn',
            'thanh-toan/zalopay/callback',
            'webhooks/sepay',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
