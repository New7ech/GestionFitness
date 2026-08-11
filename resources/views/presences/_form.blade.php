@php
    $selectedChallenge = old('challenge_id', $presence->challenge_id);
    $selectedStatus = old('status', $presence->status?->value ?? 'presente');
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
            <label for="challenge_id">Challenge <span class="text-danger">*</span></label>
            @if ($lockedChallenge)
                <input type="hidden" name="challenge_id" value="{{ $presence->challenge_id }}">
                <input type="text" id="challenge_id" class="form-control @error('challenge_id') is-invalid @enderror" disabled value="{{ $presence->challenge->participante->full_name }} - {{ $presence->challenge->challengeType->label }} - {{ $presence->challenge->start_date->format('d/m/Y') }}">
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
            <label for="attendance_date">Date <span class="text-danger">*</span></label>
            <input type="date" name="attendance_date" id="attendance_date" class="form-control @error('attendance_date') is-invalid @enderror" required value="{{ old('attendance_date', optional($presence->attendance_date)->format('Y-m-d') ?? now()->toDateString()) }}">
            @error('attendance_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
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
    <label for="comment">Commentaire</label>
    <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" rows="3">{{ old('comment', $presence->comment) }}</textarea>
    @error('comment')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
