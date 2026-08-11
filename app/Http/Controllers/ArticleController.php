<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Categorie;
use App\Models\Emplacement;
use App\Models\Fournisseur;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $articles = Article::query()
            ->when($search, fn ($query, $term) => $query->searchByText($term))
            ->latest()
            ->get();

        return view('articles.index', compact('articles'));
    }

    public function create(): View
    {
        $categories = Categorie::query()->orderBy('name')->get();
        $fournisseurs = Fournisseur::query()->orderBy('name')->get();
        $emplacements = Emplacement::query()->orderBy('name')->get();

        return view('articles.create', compact('categories', 'fournisseurs', 'emplacements'));
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $validatedData['est_visible'] = $request->has('est_visible')
            ? $request->boolean('est_visible')
            : true;

        if (empty($validatedData['slug']) && ! empty($validatedData['name'])) {
            $validatedData['slug'] = $this->generateUniqueSlug($validatedData['name']);
        }

        $validatedData['prix_promotionnel'] = $validatedData['prix_promotionnel'] ?? null;
        $validatedData['created_by'] = auth()->id();

        if ($request->hasFile('image_principale')) {
            $validatedData['image_principale'] = $request
                ->file('image_principale')
                ->store('articles_images', 'public');
        }

        Article::create($validatedData);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Article créé avec succès.');
    }

    public function show(Article $article): View
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article): View
    {
        $categories = Categorie::query()->orderBy('name')->get();
        $fournisseurs = Fournisseur::query()->orderBy('name')->get();
        $emplacements = Emplacement::query()->orderBy('name')->get();

        return view('articles.edit', compact('article', 'categories', 'fournisseurs', 'emplacements'));
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $validatedData = $request->validated();

        if ($request->has('est_visible')) {
            $validatedData['est_visible'] = $request->boolean('est_visible');
        }

        if (empty($validatedData['slug']) && ! empty($validatedData['name'])) {
            $validatedData['slug'] = $this->generateUniqueSlug($validatedData['name'], $article->id);
        }

        $validatedData['prix_promotionnel'] = $validatedData['prix_promotionnel'] ?? null;

        if ($request->boolean('supprimer_image_principale')) {
            if ($article->image_principale) {
                Storage::disk('public')->delete($article->image_principale);
            }

            $validatedData['image_principale'] = null;
        }

        if ($request->hasFile('image_principale')) {
            if ($article->image_principale) {
                Storage::disk('public')->delete($article->image_principale);
            }

            $validatedData['image_principale'] = $request
                ->file('image_principale')
                ->store('articles_images', 'public');
        }

        $validatedData['updated_by'] = auth()->id();
        $article->update($validatedData);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Article mis à jour avec succès.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        if ($article->image_principale) {
            Storage::disk('public')->delete($article->image_principale);
        }

        $article->delete();

        return redirect()
            ->route('articles.index')
            ->with('success', 'Article supprimé avec succès.');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreArticleId = null): string
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $counter = 1;

        while (
            Article::query()
                ->where('slug', $slug)
                ->when($ignoreArticleId, fn ($query) => $query->where('id', '!=', $ignoreArticleId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

