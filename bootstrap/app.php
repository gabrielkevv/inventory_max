<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

// Di Vercel, pakai URL deployment (VERCEL_URL) sebagai APP_URL bila belum diisi.
// Tidak berpengaruh saat APP_URL sudah diatur atau di luar Vercel.
if (($vercelUrl = env('VERCEL_URL')) && !env('APP_URL')) {
    $vercelUrl = 'https://'.$vercelUrl;
    $_ENV['APP_URL'] = $vercelUrl;
    putenv('APP_URL='.$vercelUrl);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Di balik proxy Vercel, TLS di-terminate di edge sehingga lambda melihat
        // request sebagai http. Percayai header forwarded (X-Forwarded-Proto)
        // supaya URL yang digenerate memakai https — tanpa ini aset dimuat
        // lewat http:// dan diblokir browser sebagai mixed content.
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 419 (token CSRF tidak cocok) jangan dibiarkan jadi halaman
        // "Page Expired" yang buntu. Kasus paling umum: form login yang
        // dibuka lama lalu disubmit setelah sesi berakhir / logout di tab
        // lain, atau form lama yang dipulihkan browser (bfcache) via tombol
        // back dengan token yang sudah tidak cocok lagi.
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi telah kedaluwarsa. Muat ulang halaman lalu coba lagi.'], 419);
            }

            // Khusus login: kembalikan ke form login (token baru ikut
            // dirender) dengan pesan, supaya pengguna tinggal coba lagi.
            // routeIs('login.store') ternyata tidak menyala di runtime Vercel
            // (redirect khusus login tidak pernah terpilih saat 419), jadi
            // tambahkan cek path+method yang tidak bergantung pada nama route.
            if ($request->routeIs('login.store') || ($request->is('login') && $request->isMethod('POST'))) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi halaman login telah kedaluwarsa. Silakan coba lagi.'])
                    ->withInput($request->only('email'));
            }

            return response()->view('errors.419', [], 419);
        });
    })->create();
