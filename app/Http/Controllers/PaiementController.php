<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMode;
use App\Enums\PaymentType;
use App\Http\Requests\StorePaiementRequest;
use App\Http\Requests\UpdatePaiementRequest;
use App\Models\Challenge;
use App\Models\Paiement;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Paiement::class);

        $paiements = Paiement::query()
            ->with(['challenge.participante', 'challenge.challengeType', 'recu'])
            ->when($request->filled('challenge_id'), fn ($query) => $query->where('challenge_id', $request->input('challenge_id')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->whereHas('challenge.participante', function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
            })
            ->latest('payment_date')
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', [
            'paiements' => $paiements,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Paiement::class);

        return view('payments.create', $this->formData(new Paiement([
            'challenge_id' => $request->integer('challenge_id') ?: null,
            'type' => PaymentType::Paiement,
            'payment_date' => now()->toDateString(),
        ])));
    }

    public function store(StorePaiementRequest $request): RedirectResponse
    {
        $this->authorize('create', Paiement::class);

        $paiement = $this->paymentService->create($request->validated(), $request->user()->id);

        return redirect()
            ->route('payments.show', $paiement)
            ->with('success', 'Paiement enregistré avec succès.');
    }

    public function show(Paiement $paiement): View
    {
        $this->authorize('view', $paiement);

        $paiement->load(['challenge.participante', 'challenge.challengeType', 'recu', 'recordedBy']);

        return view('payments.show', [
            'paiement' => $paiement,
            'remainingAmount' => $this->paymentService->remainingAmount($paiement->challenge),
        ]);
    }

    public function edit(Paiement $paiement): View
    {
        $this->authorize('update', $paiement);

        return view('payments.edit', $this->formData($paiement));
    }

    public function update(UpdatePaiementRequest $request, Paiement $paiement): RedirectResponse
    {
        $this->authorize('update', $paiement);

        $this->paymentService->update($paiement, $request->validated(), $request->user()->id);

        return redirect()
            ->route('payments.show', $paiement)
            ->with('success', 'Paiement mis à jour avec succès.');
    }

    public function destroy(Paiement $paiement): RedirectResponse
    {
        $this->authorize('delete', $paiement);

        $challenge = $paiement->challenge;
        $this->paymentService->delete($paiement);

        return redirect()
            ->route('challenges.show', $challenge)
            ->with('success', 'Paiement supprimé avec succès.');
    }

    private function formData(Paiement $paiement): array
    {
        return [
            'paiement' => $paiement,
            'challenges' => Challenge::query()
                ->with(['participante', 'challengeType'])
                ->latest()
                ->get(),
            'types' => PaymentType::cases(),
            'modes' => PaymentMode::cases(),
        ];
    }
}
