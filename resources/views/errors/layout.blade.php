<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('code') — {{ config('app.name', 'Gestion de Stock') }}</title>
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
    <style>
        body { background: linear-gradient(135deg, #1a2035 0%, #253159 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; }
        .error-box { background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.3); padding: 3rem 2.5rem; max-width: 480px; width: 100%; }
        .error-code { font-size: 6rem; font-weight: 800; line-height: 1; color: #177dff; margin-bottom: .5rem; }
        .error-icon { font-size: 3rem; margin-bottom: 1rem; }
        h2 { font-weight: 700; color: #1a2035; }
        p { color: #6c757d; }
    </style>
</head>
<body>
<div class="error-box">
    <div class="error-icon">@yield('icon')</div>
    <div class="error-code">@yield('code')</div>
    <h2 class="mb-2">@yield('title')</h2>
    <p class="mb-4">@yield('message')</p>
    <a href="{{ url('/') }}" class="btn btn-primary me-2">
        <i class="fas fa-home me-1"></i> Tableau de bord
    </a>
    <a href="javascript:history.back()" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour
    </a>
</div>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
</body>
</html>
