<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Http\Requests\StoreFournisseurRequest;
use App\Http\Requests\UpdateFournisseurRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FournisseurController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $fournisseurs = Fournisseur::query()
            ->when($search, function ($query, $term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('nom_entreprise', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('fournisseurs.index', compact('fournisseurs'));
    }

    public function create(): View
    {
        return view('fournisseurs.create');
    }

    public function store(StoreFournisseurRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        
        // Gestion de la photo
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('fournisseurs/photos', 'public');
            $validatedData['photo'] = $path;
        }
        
        $fournisseur = Fournisseur::create($validatedData);

        return redirect()
            ->route('fournisseurs.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(Fournisseur $fournisseur): View
    {
        return view('fournisseurs.show', compact('fournisseur'));
    }

    public function edit(Fournisseur $fournisseur): View
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(UpdateFournisseurRequest $request, Fournisseur $fournisseur): RedirectResponse
    {
        $validatedData = $request->validated();
        
        // Gestion de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($fournisseur->photo && Storage::disk('public')->exists($fournisseur->photo)) {
                Storage::disk('public')->delete($fournisseur->photo);
            }
            
            $path = $request->file('photo')->store('fournisseurs/photos', 'public');
            $validatedData['photo'] = $path;
        }
        
        $fournisseur->update($validatedData);

        return redirect()
            ->route('fournisseurs.index')
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Fournisseur $fournisseur): RedirectResponse
    {
        if ($fournisseur->articles()->exists()) {
            return redirect()
                ->route('fournisseurs.index')
                ->with('error', 'Suppression impossible: ce fournisseur est lié à des articles.');
        }

        $fournisseur->delete();

        return redirect()
            ->route('fournisseurs.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }
}

