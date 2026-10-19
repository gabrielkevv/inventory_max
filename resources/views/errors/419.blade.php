<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi Kedaluwarsa · {{ config('app.name') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f4f6f8; color: #1f2937; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; padding: 1rem; }
        .card { max-width: 26rem; width: 100%; background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 2rem; text-align: center; box-shadow: 0 1px 3px rgba(0, 0, 0, .06); }
        .code { font-size: 2.5rem; font-weight: 700; color: #dc3545; margin-bottom: .25rem; }
        h1 { font-size: 1.2rem; margin: 0 0 .5rem; }
        p { color: #6b7280; font-size: .95rem; line-height: 1.55; margin: 0 0 1.5rem; }
        a { display: inline-block; background: #0d6efd; color: #fff; text-decoration: none; padding: .6rem 1.4rem; border-radius: .4rem; font-size: .95rem; }
        a:hover { background: #0b5ed7; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">419</div>
        <h1>Sesi Kedaluwarsa</h1>
        <p>Halaman ini sudah kedaluwarsa karena sesi berakhir atau token keamanan tidak cocok. Muat ulang halaman lalu coba lagi.</p>
        <a href="{{ url('/') }}">Kembali ke Beranda</a>
    </div>
</body>
</html>
