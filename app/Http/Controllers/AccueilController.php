<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Facture;
use App\Models\Fournisseur;
use App\Models\User;
use Carbon\Carbon;

class AccueilController extends Controller
{
    public function index()
    {
        $now           = Carbon::now();
        $startOfMonth  = $now->copy()->startOfMonth();
        $endOfMonth    = $now->copy()->endOfMonth();

        // Factures
        $nombreFactures            = Facture::query()->count();
        $nombreFacturesPayees      = Facture::query()->where('statut_paiement', Facture::STATUS_PAYEE)->count();
        $nombreFacturesImpayees    = Facture::query()->where('statut_paiement', Facture::STATUS_IMPAYEE)->count();
        $nombreFacturesMoisCourant = Facture::query()->whereBetween('date_facture', [$startOfMonth, $endOfMonth])->count();
        $montantImpayes            = Facture::query()->where('statut_paiement', Facture::STATUS_IMPAYEE)->sum('montant_ttc');
        $montantTotal              = Facture::query()->whereBetween('date_facture', [$startOfMonth, $endOfMonth])->sum('montant_ttc');
        $chiffreAffairesMoisCourant = Facture::query()
            ->whereBetween('date_facture', [$startOfMonth, $endOfMonth])
            ->where('statut_paiement', Facture::STATUS_PAYEE)
            ->sum('montant_ttc');

        $montantCarte   = Facture::query()->where('mode_paiement', 'carte')->sum('montant_ttc');
        $montantCheque  = Facture::query()->where('mode_paiement', 'chèque')->sum('montant_ttc');
        $montantEspeces = Facture::query()->where('mode_paiement', 'espèces')->sum('montant_ttc');

        // Articles & stock
        $nombreArticles         = Article::query()->count();
        $articlesEnAlerteStock  = Article::query()->where('quantite', '<=', 5)->count();
        $articlesRecents        = Article::query()->with('categorie')->latest('updated_at')->limit(5)->get();

        // Autres entités
        $nombreFournisseurs = Fournisseur::query()->count();
        $nombreUtilisateurs = User::query()->count();
        $nombreCategories   = Categorie::query()->count();

        // Listes
        $facturesImpayees = Facture::query()->where('statut_paiement', Facture::STATUS_IMPAYEE)->latest('date_facture')->get();
        $facturesRecentes = Facture::query()->latest('date_facture')->limit(10)->get();
        $paiementModes    = Facture::query()
            ->selectRaw('mode_paiement, COUNT(*) as count, SUM(montant_ttc) as total')
            ->whereBetween('date_facture', [$startOfMonth, $endOfMonth])
            ->groupBy('mode_paiement')
            ->get();

        // Graphique articles par catégorie
        $articlesParCategorie = Article::query()
            ->join('categories', 'articles.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category, COUNT(*) as count')
            ->groupBy('categories.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $articlesParCategorieLabels = $articlesParCategorie->pluck('category')->toArray();
        $articlesParCategorieData   = $articlesParCategorie->pluck('count')->toArray();

        // Graphique ventes journalières (7 derniers jours)
        $ventesJournalieres = $this->ventesJournalieres7Jours();

        // Graphique impayés par mois
        $driver          = Facture::query()->getConnection()->getDriverName();
        $monthExpression = $driver === 'sqlite'
            ? "CAST(strftime('%m', date_facture) AS INTEGER)"
            : 'MONTH(date_facture)';

        $totauxImpayesParMois = Facture::query()
            ->selectRaw($monthExpression . ' as mois, SUM(montant_ttc) as total')
            ->whereYear('date_facture', $now->year)
            ->where('statut_paiement', Facture::STATUS_IMPAYEE)
            ->groupByRaw($monthExpression)
            ->pluck('total', 'mois');

        $labels = [];
        $data   = [];
        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::create($now->year, $month, 1)->translatedFormat('M');
            $data[]   = (float) ($totauxImpayesParMois[$month] ?? 0);
        }

        return view('accueil.index', compact(
            'nombreFactures',
            'montantTotal',
            'nombreFacturesPayees',
            'nombreFacturesImpayees',
            'montantImpayes',
            'nombreFacturesMoisCourant',
            'chiffreAffairesMoisCourant',
            'montantCarte',
            'montantCheque',
            'montantEspeces',
            'nombreArticles',
            'articlesEnAlerteStock',
            'articlesRecents',
            'nombreFournisseurs',
            'nombreUtilisateurs',
            'nombreCategories',
            'facturesImpayees',
            'facturesRecentes',
            'paiementModes',
            'articlesParCategorie',
            'articlesParCategorieLabels',
            'articlesParCategorieData',
            'ventesJournalieres',
            'labels',
            'data'
        ));
    }

    private function ventesJournalieres7Jours(): array
    {
        $driver = Facture::query()->getConnection()->getDriverName();
        $labels = [];
        $data   = [];

        for ($i = 6; $i >= 0; $i--) {
            $day      = Carbon::now()->subDays($i);
            $labels[] = $day->translatedFormat('D d/m');
            $total    = Facture::query()
                ->whereDate('date_facture', $day->toDateString())
                ->where('statut_paiement', Facture::STATUS_PAYEE)
                ->sum('montant_ttc');
            $data[] = (float) $total;
        }

        return compact('labels', 'data');
    }
}
