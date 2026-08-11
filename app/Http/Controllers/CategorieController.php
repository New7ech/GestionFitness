<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $categories = Categorie::query()
            ->when($search, fn ($query, $term) => $query->where('name', 'like', "%{$term}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategorieRequest $request): RedirectResponse
    {
        Categorie::create(array_merge($request->validated(), [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]));

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function show(Categorie $categorie): View
    {
        return view('categories.show', compact('categorie'));
    }

    public function edit(Categorie $categorie): View
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(UpdateCategorieRequest $request, Categorie $categorie): RedirectResponse
    {
        $categorie->update(array_merge($request->validated(), [
            'updated_by' => auth()->id(),
        ]));

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Categorie $categorie): RedirectResponse
    {
        if ($categorie->articles()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Suppression impossible: cette catégorie est utilisée par des articles.');
        }

        $categorie->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}

