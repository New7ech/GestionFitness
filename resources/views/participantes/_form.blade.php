@php
    $selectedStatus = old('status', $participante->status?->value ?? 'active');
@endphp

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreur !</strong> Veuillez corriger les erreurs ci-dessous.
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="first_name">Prénom <span class="text-danger">*</span></label>
            <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" required value="{{ old('first_name', $participante->first_name) }}">
            @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="last_name">Nom <span class="text-danger">*</span></label>
            <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" required value="{{ old('last_name', $participante->last_name) }}">
            @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="phone">Téléphone <span class="text-danger">*</span></label>
            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" required value="{{ old('phone', $participante->phone) }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $participante->email) }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="address">Adresse</label>
    <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $participante->address) }}">
    @error('address')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="birthdate">Date de naissance</label>
            <input type="date" name="birthdate" id="birthdate" class="form-control @error('birthdate') is-invalid @enderror" value="{{ old('birthdate', optional($participante->birthdate)->format('Y-m-d')) }}">
            @error('birthdate')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="registration_date">Date d'inscription <span class="text-danger">*</span></label>
            <input type="date" name="registration_date" id="registration_date" class="form-control @error('registration_date') is-invalid @enderror" required value="{{ old('registration_date', optional($participante->registration_date)->format('Y-m-d') ?? now()->toDateString()) }}">
            @error('registration_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="status">Statut <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="photo">Photo</label>
    <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp">
    @error('photo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($participante->exists && $participante->photo_path)
        <div class="mt-3">
            <img src="{{ $participante->photo_url }}" alt="Photo de {{ $participante->full_name }}" class="img-thumbnail" style="width: 96px; height: 96px; object-fit: cover;">
        </div>
    @endif
</div>

<div class="form-group form-check">
    <input type="hidden" name="has_cesarean" value="0">
    <input type="checkbox" name="has_cesarean" id="has_cesarean" value="1" class="form-check-input @error('has_cesarean') is-invalid @enderror" @checked(old('has_cesarean', $participante->has_cesarean))>
    <label class="form-check-label" for="has_cesarean">Césarienne déclarée</label>
    @error('has_cesarean')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="cesarean_comment">Commentaire césarienne</label>
    <textarea name="cesarean_comment" id="cesarean_comment" class="form-control @error('cesarean_comment') is-invalid @enderror" rows="2">{{ old('cesarean_comment', $participante->cesarean_comment) }}</textarea>
    @error('cesarean_comment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="health_notes">Notes santé</label>
    <textarea name="health_notes" id="health_notes" class="form-control @error('health_notes') is-invalid @enderror" rows="3">{{ old('health_notes', $participante->health_notes) }}</textarea>
    @error('health_notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if (session('warning') || old('confirm_duplicate_phone'))
    <div class="form-group form-check">
        <input type="hidden" name="confirm_duplicate_phone" value="0">
        <input type="checkbox" name="confirm_duplicate_phone" id="confirm_duplicate_phone" value="1" class="form-check-input" @checked(old('confirm_duplicate_phone'))>
        <label class="form-check-label" for="confirm_duplicate_phone">Confirmer l'inscription malgré le téléphone déjà utilisé</label>
    </div>
@endif
