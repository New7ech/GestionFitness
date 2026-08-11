<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFactureRequest;
use App\Http\Requests\UpdateFactureRequest;
use App\Models\Article;
use App\Models\Facture;
use App\Services\FactureService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class FactureController extends Controller
{
    public function __construct(private readonly FactureService $factureService)
    {
    }

    public function index(Request $request): View
    {
        $factures = Facture::query()
            ->applyFilters($request->only('search'))
            ->latest('date_facture')
            ->paginate(15)
            ->withQueryString();

        return view('factures.index', compact('factures'));
    }

    public function create(): View
    {
        return view('factures.create', [
            'articles' => Article::query()
                ->orderBy('name')
                ->get(['id', 'name', 'prix', 'quantite']),
        ]);
    }

    public function store(StoreFactureRequest $request)
    {
        try {
            $payload = $this->factureService->create($request->validated());
            $fileName = 'Facture_' . ($payload['facture']->numero ?? $payload['facture']->id) . '.pdf';

            return Pdf::loadView('factures.pdf', $payload)->download($fileName);
        } catch (ValidationException $exception) {
            return redirect()->back()
                ->withInput()
                ->withErrors($exception->errors());
        } catch (Throwable $exception) {
            Log::error('Erreur création facture', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Une erreur est survenue pendant la création de la facture.']);
        }
    }

    public function show(Facture $facture): View
    {
        $facture->load('articles');

        return view('factures.show', compact('facture'));
    }

    public function edit(Facture $facture): View
    {
        $facture->load('articles');

        return view('factures.edit', [
            'facture' => $facture,
            'articles' => Article::query()
                ->orderBy('name')
                ->get(['id', 'name', 'prix', 'quantite']),
        ]);
    }

    public function update(UpdateFactureRequest $request, Facture $facture): RedirectResponse
    {
        try {
            $this->factureService->update($facture, $request->validated());

            return redirect()
                ->route('factures.index')
                ->with('success', 'Facture mise à jour avec succès.');
        } catch (ValidationException $exception) {
            return redirect()->back()
                ->withInput()
                ->withErrors($exception->errors());
        } catch (Throwable $exception) {
            Log::error('Erreur mise à jour facture', [
                'facture_id' => $facture->id,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Une erreur est survenue pendant la mise à jour de la facture.']);
        }
    }

    public function destroy(Facture $facture): RedirectResponse
    {
        try {
            $this->factureService->delete($facture);

            return redirect()
                ->route('factures.index')
                ->with('success', 'Facture supprimée avec succès.');
        } catch (Throwable $exception) {
            Log::error('Erreur suppression facture', [
                'facture_id' => $facture->id,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()
                ->route('factures.index')
                ->with('error', 'Impossible de supprimer cette facture pour le moment.');
        }
    }

    public function genererPdf(Facture $facture)
    {
        $payload = $this->factureService->buildPdfPayload($facture);
        $fileName = 'Facture_' . ($payload['facture']->numero ?? $payload['facture']->id) . '.pdf';

        return Pdf::loadView('factures.pdf', $payload)->download($fileName);
    }
}

