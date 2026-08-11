<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta charset="UTF-8" />
    <title>{{ config('app.name', 'Gestion de Stock et Facturation') }} - @yield('title', 'Tableau de bord')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
          urls: ["{{ asset('assets/css/fonts.min.css') }}"],
        },
        active: function () { sessionStorage.fonts = true; },
      });
    </script>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/article-images.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/dashboard-enhanced.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/page-header-fix.css') }}" />

    @stack('styles')
  </head>
  <body>
    <div class="wrapper">
      @include('layouts.sidebar')

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            @include('layouts.logoheader')
          </div>
          @include('layouts.navhead')
        </div>

        <div class="container" style="margin-top: 20px;">
          <div class="page-inner">

            {{-- Messages flash --}}
            @if (session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if (session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if (session('warning'))
              <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif
            @if (session('info'))
              <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @yield('contenus')
          </div>
        </div>

        @include('layouts.footer')
      </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- SweetAlert v1 → v2 bridge -->
    <script>
      window.Swal = window.Swal || {};
      if (typeof window.Swal.fire !== 'function') {
        window.Swal.fire = function (optionsOrTitle, text, icon) {
          const options = (typeof optionsOrTitle === 'object' && optionsOrTitle !== null)
            ? optionsOrTitle
            : { title: optionsOrTitle, text: text, icon: icon };
          if (typeof window.swal === 'function') {
            let msg  = options.text || '';
            let icn  = options.icon === 'question' ? 'info' : options.icon;
            if (!msg && options.html) {
              const tmp = document.createElement('div');
              tmp.innerHTML = options.html;
              msg = tmp.textContent || tmp.innerText || '';
            }
            const buttons = options.showCancelButton
              ? { cancel: { text: options.cancelButtonText || 'Annuler', value: false, visible: true }, confirm: { text: options.confirmButtonText || 'OK', value: true, visible: true } }
              : { confirm: { text: options.confirmButtonText || 'OK', value: true, visible: true } };
            return window.swal({ title: options.title || '', text: msg, icon: icn, buttons, dangerMode: options.icon === 'warning' })
              .then(value => ({ isConfirmed: Boolean(value), isDismissed: !value, value }));
          }
          const confirmed = window.confirm(options.text || options.title || 'Confirmer cette action ?');
          return Promise.resolve({ isConfirmed: confirmed, isDismissed: !confirmed, value: confirmed });
        };
      }
    </script>

    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
      // Validation Bootstrap pour tous les formulaires .needs-validation
      (() => {
        'use strict';
        document.querySelectorAll('.needs-validation').forEach(form => {
          form.addEventListener('submit', e => {
            if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
            form.classList.add('was-validated');
          }, false);
        });
      })();

      // Sparklines du tableau de bord
      if ($("#lineChart").length) {
        $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], { type: "line", height: "70", width: "100%", lineWidth: "2", lineColor: "#177dff", fillColor: "rgba(23, 125, 255, 0.14)" });
      }
      if ($("#lineChart2").length) {
        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], { type: "line", height: "70", width: "100%", lineWidth: "2", lineColor: "#f3545d", fillColor: "rgba(243, 84, 93, .14)" });
      }
      if ($("#lineChart3").length) {
        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], { type: "line", height: "70", width: "100%", lineWidth: "2", lineColor: "#ffa534", fillColor: "rgba(255, 165, 52, .14)" });
      }
    </script>

    @stack('scripts')
  </body>
</html>
