<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>Connexion — {{ config('app.name', 'Gestion de Stock') }}</title>
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
    <style>
        body { background: linear-gradient(135deg, #1a2035 0%, #253159 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.35); width: 100%; max-width: 420px; padding: 2.5rem 2rem; }
        .login-logo { text-align: center; margin-bottom: 1.5rem; }
        .login-logo img { width: 64px; height: 64px; object-fit: contain; }
        .login-logo h3 { font-weight: 700; color: #1a2035; margin-top: .5rem; font-size: 1.2rem; }
        .btn-login { background: #177dff; border-color: #177dff; font-weight: 600; letter-spacing: .5px; padding: .65rem; }
        .btn-login:hover { background: #1266cc; border-color: #1266cc; }
        .form-control:focus { border-color: #177dff; box-shadow: 0 0 0 .2rem rgba(23,125,255,.25); }
        .input-group-text { background: #f8f9fa; }
        .toggle-password { cursor: pointer; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <img src="{{ asset('assets/img/kaiadmin/logo.svg') }}" alt="Logo" onerror="this.style.display='none'" />
        <h3>{{ config('app.name', 'Gestion de Stock') }}</h3>
        <p class="text-muted small mb-0">Connectez-vous à votre espace</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Adresse email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    placeholder="exemple@domaine.com"
                    autocomplete="email"
                    autofocus
                    required
                />
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                />
                <span class="input-group-text toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                </span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small" for="remember">Se souvenir de moi</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-login w-100">
            <i class="fas fa-sign-in-alt me-2"></i> Se connecter
        </button>
    </form>

    <p class="text-center text-muted small mt-4 mb-0">
        &copy; {{ date('Y') }} {{ config('app.name', 'Gestion de Stock') }}
    </p>
</div>

<script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    (() => {
        'use strict';
        document.querySelectorAll('.needs-validation').forEach(form => {
            form.addEventListener('submit', e => {
                if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
</body>
</html>
