@extends('layouts.app')

@section('title', "Modifier la Permission : " . $permission->name)

@section('contenus')
<div class="page-inner">
<div class="page-header">
    <h3 class="fw-bold mb-3">Gestion des Acces</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home">
            <a href="{{ route('accueil') }}">
                <i class="icon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('permissions.index') }}">Permissions</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Modifier : {{ Str::limit($permission->name, 40) }}</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Formulaire de Modification de Permission</div>
                <div class="card-category">Guard actuel: `{{ $permission->guard_name }}`.</div>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Erreur.</strong> Verifiez les champs ci-dessous.
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('permissions.update', $permission->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Nom de la permission <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            required
                            pattern="[a-z0-9]+([.-][a-z0-9]+)*"
                            value="{{ old('name', $permission->name) }}"
                            placeholder="ex: articles.read"
                        >
                        <small class="form-text text-muted">
                            Format recommande: `module.action` (ex: `articles.read`, `users.delete`).
                            Espaces, underscores et accents sont normalises automatiquement.
                        </small>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="invalid-feedback">Utilisez minuscules, chiffres, points ou tirets.</div>
                    </div>

                    <div class="card-action text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre a Jour la Permission
                        </button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('name');
        if (!input) {
            return;
        }

        input.addEventListener('input', function () {
            const normalized = input.value
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim()
                .replace(/[\s_]+/g, '-')
                .replace(/[^a-z0-9.-]+/g, '-')
                .replace(/[-.]{2,}/g, '-')
                .replace(/^[-.]+|[-.]+$/g, '');

            if (input.value !== normalized) {
                input.value = normalized;
            }
        });
    });
</script>
@endpush
