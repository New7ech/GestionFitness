<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmplacementRequest;
use App\Http\Requests\UpdateEmplacementRequest;
use App\Models\Emplacement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmplacementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $emplacements = Emplacement::query()
            ->when($search, fn ($query, $term) => $query->where('name', 'like', "%{$term}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('emplacements.index', compact('emplacements'));
    }

    public function create(): View
    {
        return view('emplacements.create');
    }

    public function store(StoreEmplacementRequest $request): RedirectResponse
    {
        Emplacement::create($request->validated());

        return redirect()
            ->route('emplacements.index')
            ->with('success', 'Emplacement créé avec succès.');
    }

    public function show(Emplacement $emplacement): View
    {
        return view('emplacements.show', compact('emplacement'));
    }

    public function edit(Emplacement $emplacement): View
    {
        return view('emplacements.edit', compact('emplacement'));
    }

    public function update(UpdateEmplacementRequest $request, Emplacement $emplacement): RedirectResponse
    {
        $emplacement->update($request->validated());

        return redirect()
            ->route('emplacements.index')
            ->with('success', 'Emplacement mis à jour avec succès.');
    }

    public function destroy(Emplacement $emplacement): RedirectResponse
    {
        if ($emplacement->articles()->exists()) {
            return redirect()
                ->route('emplacements.index')
                ->with('error', 'Suppression impossible: cet emplacement est utilisé par des articles.');
        }

        $emplacement->delete();

        return redirect()
            ->route('emplacements.index')
            ->with('success', 'Emplacement supprimé avec succès.');
    }
}

