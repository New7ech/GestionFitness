@php
    $selectedChallenge = old('challenge_id', $mesure->challenge_id);
    $selectedStage = old('stage', $mesure->stage?->value ?? 'initiale');
    $savedValues = $mesure->relationLoaded('values')
        ? $mesure->values->pluck('value', 'measurement_type_id')->toArray()
        : [];
    $selectedValues = old('measurement_values', $savedValues);
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

@if ($historizedUpdate)
    <div class="alert alert-warning">
        Cette correction créera une nouvelle ligne de mesure. La mesure actuelle restera conservée dans l’historique.
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="challenge_id">Challenge <span class="text-danger">*</span></label>
            @if ($historizedUpdate)
                <input type="hidden" name="challenge_id" value="{{ $mesure->challenge_id }}">
                <input type="text" id="challenge_id" class="form-control @error('challenge_id') is-invalid @enderror" disabled value="{{ $mesure->challenge->participante->full_name }} - {{ $mesure->challenge->challengeType->label }} - {{ $mesure->challenge->start_date->format('d/m/Y') }}">
            @else
                <select name="challenge_id" id="challenge_id" class="form-select @error('challenge_id') is-invalid @enderror" required>
                    <option value="">-- Choisir un challenge --</option>
                    @foreach ($challenges as $challenge)
                        <option value="{{ $challenge->id }}" @selected((int) $selectedChallenge === $challenge->id)>
                            {{ $challenge->participante->full_name }} - {{ $challenge->challengeType->label }} - {{ $challenge->start_date->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            @endif
            @error('challenge_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="measured_at">Date <span class="text-danger">*</span></label>
            <input type="date" name="measured_at" id="measured_at" class="form-control @error('measured_at') is-invalid @enderror" required value="{{ old('measured_at', optional($mesure->measured_at)->format('Y-m-d') ?? now()->toDateString()) }}">
            @error('measured_at')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="stage">Étape <span class="text-danger">*</span></label>
            <select name="stage" id="stage" class="form-select @error('stage') is-invalid @enderror" required>
                @foreach ($stages as $stage)
                    <option value="{{ $stage->value }}" @selected($selectedStage === $stage->value)>{{ $stage->label() }}</option>
                @endforeach
            </select>
            @error('stage')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="weight">Poids (kg) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" required value="{{ old('weight', $mesure->weight) }}">
            @error('weight')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="waist">Tour de taille (cm)</label>
            <input type="number" step="0.01" min="0.01" name="waist" id="waist" class="form-control @error('waist') is-invalid @enderror" value="{{ old('waist', $mesure->waist) }}">
            @error('waist')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

@if ($measurementTypes->isNotEmpty())
    <div class="row">
        @foreach ($measurementTypes as $measurementType)
            <div class="col-md-3">
                <div class="form-group">
                    <label for="measurement_value_{{ $measurementType->id }}">{{ $measurementType->label }} ({{ $measurementType->unit }})</label>
                    <input type="number" step="0.01" min="0.01" name="measurement_values[{{ $measurementType->id }}]" id="measurement_value_{{ $measurementType->id }}" class="form-control @error('measurement_values.'.$measurementType->id) is-invalid @enderror" value="{{ $selectedValues[$measurementType->id] ?? '' }}">
                    @error('measurement_values.'.$measurementType->id)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="form-group">
    <label for="comment">Commentaire</label>
    <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" rows="3">{{ old('comment', $mesure->comment) }}</textarea>
    @error('comment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
