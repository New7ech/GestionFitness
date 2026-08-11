<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Recu;
use App\Services\RecuService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecuController extends Controller
{
    public function __construct(private readonly RecuService $recuService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Recu::class);

        $recus = Recu::query()
            ->with('paiement.challenge.participante')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(function ($nestedQuery) use ($term): void {
                    $nestedQuery
                        ->where('receipt_number', 'like', "%{$term}%")
                        ->orWhere('participante_full_name', 'like', "%{$term}%");
                });
            })
            ->latest('issued_at')
            ->paginate(10)
            ->withQueryString();

        return view('recus.index', compact('recus'));
    }

    public function store(Paiement $paiement): RedirectResponse
    {
        $this->authorize('generate', [Recu::class, $paiement]);

        $recu = $this->recuService->generate($paiement, auth()->id());

        return redirect()
            ->route('recus.show', $recu)
            ->with('success', 'Reçu généré avec succès.');
    }

    public function show(Recu $recu): View
    {
        $this->authorize('view', $recu);

        return view('recus.show', [
            'recu' => $recu->load('paiement.challenge.participante', 'paiement.challenge.challengeType', 'generatedBy'),
        ]);
    }

    public function pdf(Recu $recu)
    {
        $this->authorize('view', $recu);

        $payload = $this->recuService->buildPdfPayload($recu);

        return Pdf::loadView('recus.pdf', $payload)
            ->download('Recu_'.$payload['recu']->receipt_number.'.pdf');
    }
}
