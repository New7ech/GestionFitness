<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Facture;
use App\Models\User;
use App\Notifications\StockLowNotification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class FactureService
{
    /**
     * @return array{facture: Facture, details: array<int, array{article: Article, quantity: int, prix_unitaire: float, montant_ht: float}>}
     */
    public function create(array $validated): array
    {
        return DB::transaction(function () use ($validated): array {
            $prepared = $this->prepareLines($validated['articles']);

            $facture = Facture::create([
                'client_nom' => $validated['client_nom'],
                'client_prenom' => $validated['client_prenom'] ?? null,
                'client_adresse' => $validated['client_adresse'] ?? null,
                'client_telephone' => $validated['client_telephone'] ?? null,
                'client_email' => $validated['client_email'] ?? null,
                'numero' => $this->generateInvoiceNumber(),
                'date_facture' => now(),
                'montant_ht' => $prepared['montant_ht'],
                'tva' => Facture::TVA_RATE,
                'montant_ttc' => $prepared['montant_ttc'],
                'mode_paiement' => $validated['mode_paiement'] ?? null,
                'statut_paiement' => $validated['statut_paiement'],
                'date_paiement' => $validated['statut_paiement'] === Facture::STATUS_PAYEE ? now() : null,
            ]);

            $facture->articles()->sync($prepared['pivot_payload']);
            $lowStockArticles = $this->decreaseStock($prepared['stock_deltas'], $prepared['articles']);
            $this->sendLowStockNotifications($lowStockArticles);

            return $this->buildPdfPayload($facture->fresh('articles'));
        });
    }

    public function update(Facture $facture, array $validated): Facture
    {
        return DB::transaction(function () use ($facture, $validated): Facture {
            $facture->load('articles');
            $this->restoreStock($facture->articles);

            $prepared = $this->prepareLines($validated['articles']);

            $facture->update([
                'client_nom' => $validated['client_nom'],
                'client_prenom' => $validated['client_prenom'] ?? null,
                'client_adresse' => $validated['client_adresse'] ?? null,
                'client_telephone' => $validated['client_telephone'] ?? null,
                'client_email' => $validated['client_email'] ?? null,
                'montant_ht' => $prepared['montant_ht'],
                'tva' => Facture::TVA_RATE,
                'montant_ttc' => $prepared['montant_ttc'],
                'mode_paiement' => $validated['mode_paiement'] ?? null,
                'statut_paiement' => $validated['statut_paiement'],
                'date_paiement' => $validated['statut_paiement'] === Facture::STATUS_PAYEE
                    ? ($facture->date_paiement ?? now())
                    : null,
            ]);

            $facture->articles()->sync($prepared['pivot_payload']);
            $lowStockArticles = $this->decreaseStock($prepared['stock_deltas'], $prepared['articles']);
            $this->sendLowStockNotifications($lowStockArticles);

            return $facture->fresh('articles');
        });
    }

    public function delete(Facture $facture): void
    {
        DB::transaction(function () use ($facture): void {
            $facture->load('articles');

            $this->restoreStock($facture->articles);
            $facture->articles()->detach();
            $facture->delete();
        });
    }

    /**
     * @return array{facture: Facture, details: array<int, array{article: Article, quantity: int, prix_unitaire: float, montant_ht: float}>}
     */
    public function buildPdfPayload(Facture $facture): array
    {
        $facture->loadMissing('articles');

        $details = $facture->articles->map(function (Article $article): array {
            $quantity = (int) $article->pivot->quantite;
            $price = (float) $article->pivot->prix_unitaire;

            return [
                'article' => $article,
                'quantity' => $quantity,
                'prix_unitaire' => $price,
                'montant_ht' => $price * $quantity,
            ];
        })->values()->all();

        return [
            'facture' => $facture,
            'details' => $details,
        ];
    }

    /**
     * @param array<int, array{article_id: int|string, quantity: int|string}> $requestedLines
     * @return array{
     *     articles: Collection<int, Article>,
     *     details: array<int, array{article: Article, quantity: int, prix_unitaire: float, montant_ht: float}>,
     *     pivot_payload: array<int, array{quantite: int, prix_unitaire: float, created_at: \Illuminate\Support\Carbon, updated_at: \Illuminate\Support\Carbon}>,
     *     stock_deltas: array<int, int>,
     *     montant_ht: float,
     *     montant_ttc: float
     * }
     */
    private function prepareLines(array $requestedLines): array
    {
        $articleIds = collect($requestedLines)
            ->pluck('article_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        $articles = Article::query()
            ->whereIn('id', $articleIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $availableStockByArticle = $articles
            ->map(fn (Article $article): int => (int) $article->quantite)
            ->all();

        $stockDeltas = [];

        foreach ($requestedLines as $index => $line) {
            $articleId = (int) ($line['article_id'] ?? 0);
            $quantity = (int) ($line['quantity'] ?? 0);

            if (! isset($availableStockByArticle[$articleId])) {
                throw ValidationException::withMessages([
                    "articles.{$index}.article_id" => 'Article introuvable.',
                ]);
            }

            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    "articles.{$index}.quantity" => 'La quantité doit être supérieure à 0.',
                ]);
            }

            if ($quantity > $availableStockByArticle[$articleId]) {
                throw ValidationException::withMessages([
                    "articles.{$index}.quantity" => "Quantité insuffisante en stock pour l'article {$articles[$articleId]->name}.",
                ]);
            }

            $availableStockByArticle[$articleId] -= $quantity;
            $stockDeltas[$articleId] = ($stockDeltas[$articleId] ?? 0) + $quantity;
        }

        $now = now();
        $montantHT = 0.0;
        $details = [];
        $pivotPayload = [];

        foreach ($stockDeltas as $articleId => $quantity) {
            $article = $articles[$articleId];
            $prixUnitaire = (float) $article->prix;
            $montantLigne = $prixUnitaire * $quantity;

            $pivotPayload[$articleId] = [
                'quantite' => $quantity,
                'prix_unitaire' => $prixUnitaire,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $details[] = [
                'article' => $article,
                'quantity' => $quantity,
                'prix_unitaire' => $prixUnitaire,
                'montant_ht' => $montantLigne,
            ];

            $montantHT += $montantLigne;
        }

        return [
            'articles' => $articles,
            'details' => $details,
            'pivot_payload' => $pivotPayload,
            'stock_deltas' => $stockDeltas,
            'montant_ht' => $montantHT,
            'montant_ttc' => $montantHT * (1 + (Facture::TVA_RATE / 100)),
        ];
    }

    /**
     * @param Collection<int, Article> $articles
     */
    private function decreaseStock(array $stockDeltas, Collection $articles): EloquentCollection
    {
        $lowStockArticles = new EloquentCollection();

        foreach ($stockDeltas as $articleId => $quantity) {
            /** @var Article $article */
            $article = $articles->get($articleId);
            $article->decrement('quantite', $quantity);
            $article->refresh();

            if ($article->quantite < Facture::LOW_STOCK_THRESHOLD) {
                $lowStockArticles->push($article);
            }
        }

        return $lowStockArticles;
    }

    private function restoreStock(EloquentCollection $articles): void
    {
        foreach ($articles as $article) {
            $article->increment('quantite', (int) $article->pivot->quantite);
        }
    }

    private function sendLowStockNotifications(EloquentCollection $articles): void
    {
        if ($articles->isEmpty()) {
            return;
        }

        $users = User::query()
            ->where('notifications_enabled', true)
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($articles as $article) {
            Notification::send($users, new StockLowNotification($article, Facture::LOW_STOCK_THRESHOLD));
        }
    }

    private function generateInvoiceNumber(): string
    {
        $nextId = (int) Facture::withTrashed()
            ->lockForUpdate()
            ->max('id') + 1;

        return sprintf('FAC-%s-%04d', now()->year, $nextId);
    }
}

