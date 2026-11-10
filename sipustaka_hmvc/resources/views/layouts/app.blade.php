<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiPustaka') · SiPustaka</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif;color:#17233b;background:#f4f7fb;font-synthesis:none}
        *{box-sizing:border-box}body{margin:0}a{color:#315bd6;text-decoration:none}a:hover{text-decoration:underline}
        .topbar{background:#102844;color:#fff}.nav{max-width:1120px;margin:auto;padding:16px 24px;display:flex;align-items:center;gap:24px;flex-wrap:wrap}
        .brand{font-size:20px;font-weight:800;letter-spacing:-.04em;color:#fff;margin-right:auto}.nav-links{display:flex;gap:18px;align-items:center;flex-wrap:wrap}.nav-links a{color:#d9e7f5}
        .identity{color:#c4d5e8;font-size:13px}.nav form{display:inline}.logout{border:0;background:none;color:#fff;cursor:pointer;font:inherit;padding:0}
        main{max-width:1120px;margin:36px auto;padding:0 24px 60px}.page-heading{display:flex;justify-content:space-between;align-items:center;gap:20px;margin:10px 0 24px}
        h1{font-size:32px;line-height:1.15;letter-spacing:-.04em;margin:4px 0 8px}.eyebrow{font-size:11px;letter-spacing:.14em;color:#657b99;font-weight:800;margin:0}.muted{color:#697a92;margin:0}
        .button{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:9px 15px;border:0;border-radius:8px;background:#315bd6;color:white;font-family:inherit;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none}
        .button:hover{background:#2449b8;color:#fff;text-decoration:none}.button.secondary{background:#e8efff;color:#2449b8}.button.quiet{background:transparent;color:#4f617b;border:1px solid #d5deeb}
        .card,.table-wrap{background:white;border:1px solid #e1e8f1;border-radius:12px;box-shadow:0 6px 24px #223a5b0a}
        .table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;min-width:650px}th,td{text-align:left;padding:14px 16px;border-bottom:1px solid #e9eef5;font-size:14px}
        th{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#697a92;background:#f9fbfd}tbody tr:last-child td{border-bottom:0}.empty{text-align:center;padding:32px;color:#697a92}
        .actions{display:flex;gap:12px;align-items:center}.actions form{display:inline}.link{border:0;background:none;padding:0;color:#315bd6;cursor:pointer;font:inherit}.danger-text{color:#bd3447}
        .search{display:flex;gap:10px;margin-bottom:16px}.search input{flex:1}
        .form-grid{padding:24px;max-width:680px;display:grid;grid-template-columns:1fr 1fr;gap:18px}.field{display:flex;flex-direction:column;gap:7px}.field label{font-size:13px;font-weight:700;color:#354761}
        input,select{width:100%;min-height:42px;border:1px solid #cfd9e6;border-radius:7px;padding:9px 11px;background:#fff;color:#17233b;font:inherit}input:focus,select:focus{outline:2px solid #9db5ff;border-color:#7894ee}
        .form-actions{grid-column:1/-1;display:flex;gap:10px;margin-top:3px}.error{color:#bd3447;font-size:12px}.notice{padding:12px 15px;border-radius:8px;margin:0 0 18px;font-size:14px}.success{background:#e6f7ee;color:#176b3e;border:1px solid #bce7ce}.alert-error{background:#fff0f1;color:#a52a3c;border:1px solid #f1c6cc}
        .pagination{margin-top:20px}.pagination nav{display:flex;justify-content:center}.pagination nav>div:first-child{display:none}.pagination nav>div:last-child>span,.pagination nav>div:last-child>a{display:inline-block;margin:2px;padding:7px 11px;border:1px solid #d5deeb;border-radius:6px}
        .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
        .auth-shell{max-width:480px;margin:55px auto;padding:0 20px}.auth-card{padding:28px}.auth-card h1{font-size:28px}.auth-card .field{margin:16px 0}.auth-card .button{width:100%}.auth-footer{margin:20px 0;color:#697a92;text-align:center}
        @media(max-width:700px){main{margin-top:24px;padding:0 15px 40px}.nav{padding:14px 16px;gap:13px}.nav-links{order:3;width:100%;gap:13px}.identity{display:none}.page-heading{align-items:flex-start}.form-grid{grid-template-columns:1fr;padding:18px}.form-actions{grid-column:1}.search{flex-wrap:wrap}.search input{flex-basis:100%}}
    </style>
    @stack('head')
</head>
<body>
<header class="topbar">
    <nav class="nav">
        <a class="brand" href="{{ auth()->check() ? route('buku.index') : route('login') }}">SiPustaka</a>
        @auth
            <span class="identity">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</span>
            <div class="nav-links">
                <a href="{{ route('buku.index') }}">Buku</a>
                <a href="{{ route('peminjaman.index') }}">Peminjaman</a>
                @if(auth()->user()->role === 'admin')<a href="{{ route('users.index') }}">Manajemen User</a>@endif
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">Keluar</button></form>
            </div>
        @endauth
    </nav>
</header>
<main>
    @if($errors->has('user'))<div class="notice alert-error">{{ $errors->first('user') }}</div>@endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
