@php
    $selectedParticipante = old('participante_id', $challenge->participante_id);
    $selectedType = old('challenge_type_id', $challenge->challenge_type_id);
    $selectedDuration = old('duration_days', $challenge->duration_days);
    $selectedStatus = old('status', $challenge->status?->value ?? 'planifie');
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
            <label for="participante_id">Participante <span class="text-danger">*</span></label>
            <select name="participante_id" id="participante_id" class="form-select @error('participante_id') is-invalid @enderror" required>
                <option value="">-- Choisir une participante --</option>
                @foreach ($participantes as $participante)
                    <option value="{{ $participante->id }}" @selected((int) $selectedParticipante === $participante->id)>{{ $participante->full_name }} - {{ $participante->phone }}</option>
                @endforeach
            </select>
            @error('participante_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="challenge_type_id">Type de challenge <span class="text-danger">*</span></label>
            <select name="challenge_type_id" id="challenge_type_id" class="form-select @error('challenge_type_id') is-invalid @enderror" required>
                <option value="">-- Choisir un type --</option>
                @foreach ($challengeTypes as $challengeType)
                    <option value="{{ $challengeType->id }}" @selected((int) $selectedType === $challengeType->id)>{{ $challengeType->label }}</option>
                @endforeach
            </select>
            @error('challenge_type_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="start_date">Date de début <span class="text-danger">*</span></label>
            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" required value="{{ old('start_date', optional($challenge->start_date)->format('Y-m-d') ?? now()->toDateString()) }}">
            @error('start_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="duration_days">Durée <span class="text-danger">*</span></label>
            <select name="duration_days" id="duration_days" class="form-select @error('duration_days') is-invalid @enderror" required>
                @foreach ($durations as $duration)
                    <option value="{{ $duration }}" @selected((int) $selectedDuration === (int) $duration)>{{ $duration }} jours</option>
                @endforeach
            </select>
            @error('duration_days')
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

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="price">Prix <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror" required value="{{ old('price', $challenge->price) }}">
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="goal_weight">Poids objectif</label>
            <input type="number" step="0.01" min="0.01" name="goal_weight" id="goal_weight" class="form-control @error('goal_weight') is-invalid @enderror" value="{{ old('goal_weight', $challenge->goal_weight) }}">
            @error('goal_weight')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="goal_waist">Tour de taille objectif</label>
            <input type="number" step="0.01" min="0.01" name="goal_waist" id="goal_waist" class="form-control @error('goal_waist') is-invalid @enderror" value="{{ old('goal_waist', $challenge->goal_waist) }}">
            @error('goal_waist')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group">
    <label for="goal_text">Objectif principal</label>
    <textarea name="goal_text" id="goal_text" class="form-control @error('goal_text') is-invalid @enderror" rows="2">{{ old('goal_text', $challenge->goal_text) }}</textarea>
    @error('goal_text')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="goal_personal">Objectif personnel</label>
    <textarea name="goal_personal" id="goal_personal" class="form-control @error('goal_personal') is-invalid @enderror" rows="2">{{ old('goal_personal', $challenge->goal_personal) }}</textarea>
    @error('goal_personal')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="observations">Observations</label>
    <textarea name="observations" id="observations" class="form-control @error('observations') is-invalid @enderror" rows="3">{{ old('observations', $challenge->observations) }}</textarea>
    @error('observations')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@if ($challenge->exists && (session('warning') || old('confirm_schedule_change')))
    <div class="form-group form-check">
        <input type="hidden" name="confirm_schedule_change" value="0">
        <input type="checkbox" name="confirm_schedule_change" id="confirm_schedule_change" value="1" class="form-check-input" @checked(old('confirm_schedule_change'))>
        <label class="form-check-label" for="confirm_schedule_change">Confirmer le recalcul de la date de fin</label>
    </div>
@endif
