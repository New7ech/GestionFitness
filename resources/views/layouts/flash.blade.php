@php
$alertTypes = [
    'success' => ['icon' => 'fas fa-check-circle', 'class' => 'success'],
    'error'   => ['icon' => 'fas fa-times-circle', 'class' => 'danger'],
    'warning' => ['icon' => 'fas fa-exclamation-triangle', 'class' => 'warning'],
    'info'    => ['icon' => 'fas fa-info-circle', 'class' => 'info'],
    'danger'  => ['icon' => 'fas fa-times-circle', 'class' => 'danger'], // Alias for error
];
@endphp

@foreach (['success', 'error', 'warning', 'info', 'danger'] as $type)
    @if (session($type))
        @php
            $alert = $alertTypes[$type] ?? ['icon' => 'fas fa-info-circle', 'class' => 'info'];
        @endphp
        <div class="alert alert-{{ $alert['class'] }} alert-dismissible fade show" role="alert">
            <span class="{{ $alert['icon'] }} me-2"></span>
            {{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endforeach

