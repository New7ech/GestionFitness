@php
    $selectedChallenge = old('challenge_id', $paiement->challenge_id);
    $selectedType = old('type', $paiement->type?->value ?? 'paiement');
    $selectedMode = old('payment_mode', $paiement->payment_mode?->value ?? 'especes');
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

<div class="form-group">
    <label for="challenge_id">Challenge <span class="text-danger">*</span></label>
    <select name="challenge_id" id="challenge_id" class="form-select @error('challenge_id') is-invalid @enderror" required>
        <option value="">-- Choisir un challenge --</option>
        @foreach ($challenges as $challenge)
            <option value="{{ $challenge->id }}" @selected((int) $selectedChallenge === $challenge->id)>
                {{ $challenge->participante->full_name }} - {{ $challenge->challengeType->label }} - {{ $challenge->start_date->format('d/m/Y') }}
            </option>
        @endforeach
    </select>
    @error('challenge_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="amount">Montant <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" required value="{{ old('amount', $paiement->amount) }}">
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="type">Type <span class="text-danger">*</span></label>
            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" @selected($selectedType === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="payment_date">Date <span class="text-danger">*</span></label>
            <input type="date" name="payment_date" id="payment_date" class="form-control @error('payment_date') is-invalid @enderror" required value="{{ old('payment_date', optional($paiement->payment_date)->format('Y-m-d') ?? now()->toDateString()) }}">
            @error('payment_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="payment_mode">Mode de paiement <span class="text-danger">*</span></label>
    <select name="payment_mode" id="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror" required>
        @foreach ($modes as $mode)
            <option value="{{ $mode->value }}" @selected($selectedMode === $mode->value)>{{ $mode->label() }}</option>
        @endforeach
    </select>
    @error('payment_mode')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="comment">Commentaire</label>
    <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" rows="3">{{ old('comment', $paiement->comment) }}</textarea>
    @error('comment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
