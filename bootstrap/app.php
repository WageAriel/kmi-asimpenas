<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\CheckUserActive::class, // Cek user aktif di setiap request
        ]);

        // Nonaktifkan CSRF protection untuk route tertentu
        $middleware->validateCsrfTokens(except: [
            // 'hasil-seleksi-mitra',
            // 'hasil-seleksi-mitra/*',
            // 'admin/hasil-seleksi-mitra',
            // 'admin/hasil-seleksi-mitra/*',
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'guest' => RedirectIfAuthenticated::class,
        ]);

        //
    })
    ->withSchedule(function ($schedule) {
        // Jalankan command deactivate user setiap tanggal 1 Januari jam 00:00
        $schedule->command('users:deactivate-old-year')
            ->yearlyOn(1, 1, '00:00')
            ->timezone('Asia/Jakarta');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
