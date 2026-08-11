<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Facture;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    public function index(): View
    {
        // 1. totalArticlesInStock
        $totalArticlesInStock = Article::sum('quantite');

        // 2. articlesPerCategory
        $categories = Categorie::withCount('articles')->get();
        $articlesPerCategoryLabels = $categories->pluck('name')->toArray();
        $articlesPerCategoryData = $categories->pluck('articles_count')->toArray();

        // 3. lowStockArticles
        $lowStockThreshold = Facture::LOW_STOCK_THRESHOLD;
        $lowStockArticles = Article::where('quantite', '<=', $lowStockThreshold)
                                   ->select('name', 'quantite')
                                   ->orderBy('quantite', 'asc')
                                   ->get();

        // 4. totalSalesRevenueLast30Days
        $totalSalesRevenueLast30Days = Facture::where('date_facture', '>=', Carbon::now()->subDays(30))
                                              ->sum('montant_ttc');

        // 5. salesTrendLast30Days
        $salesTrendRaw = Facture::select(
                DB::raw('DATE(date_facture) as date'),
                DB::raw('SUM(montant_ttc) as total_sales')
            )
            ->where('date_facture', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $salesTrendLabels = [];
        $salesTrendData = [];
        // Initialize with all dates in the last 30 days to ensure continuity in the chart
        $period = Carbon::now()->subDays(29); // Start from 29 days ago to include today
        for ($i = 0; $i < 30; $i++) {
            $dateStr = $period->format('Y-m-d');
            $salesTrendLabels[] = $period->format('d/m'); // Format for display
            $salesDataForDate = $salesTrendRaw->firstWhere('date', $dateStr);
            $salesTrendData[] = $salesDataForDate ? $salesDataForDate->total_sales : 0;
            $period->addDay();
        }


        // 6. bestSellingArticlesLast30Days
        $bestSellingArticlesRaw = DB::table('article_facture')
            ->select('articles.name', DB::raw('SUM(article_facture.quantite) as total_quantity_sold'))
            ->join('articles', 'article_facture.article_id', '=', 'articles.id')
            ->join('factures', 'article_facture.facture_id', '=', 'factures.id')
            ->where('factures.date_facture', '>=', Carbon::now()->subDays(30))
            ->groupBy('articles.id', 'articles.name')
            ->orderByDesc('total_quantity_sold')
            ->limit(5)
            ->get();
        
        $bestSellingArticlesLabels = $bestSellingArticlesRaw->pluck('name')->toArray();
        $bestSellingArticlesData = $bestSellingArticlesRaw->pluck('total_quantity_sold')->toArray();


        return view('statistiques.index', compact(
            'totalArticlesInStock',
            'articlesPerCategoryLabels',
            'articlesPerCategoryData',
            'lowStockArticles',
            'totalSalesRevenueLast30Days',
            'salesTrendLabels',
            'salesTrendData',
            'bestSellingArticlesLabels',
            'bestSellingArticlesData'
        ));
    }
}
