<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Categorie;
use App\Models\MouvementStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalArticles = Article::count();
        $totalCategories = Categorie::count();

        $totalEntrees = MouvementStock::whereHas('typeMouvement', fn ($q) => $q->where('code', 'IN'))->count();
        $totalSorties = MouvementStock::whereHas('typeMouvement', fn ($q) => $q->where('code', 'OUT'))->count();

        $valeurStock = (float) Article::sum(DB::raw('stock_actuel * prix_achat'));

        $enRupture = Article::where('stock_actuel', '<=', 0)->count();
        $presqueRupture = Article::where('stock_actuel', '>', 0)->whereColumn('stock_actuel', '<=', 'stock_minimum')->count();

        $evolution = collect(range(6, 0))
            ->mapWithKeys(fn ($i) => [
                now()->subDays($i)->toDateString() => ['entrees' => 0, 'sorties' => 0],
            ]);

        MouvementStock::with('typeMouvement')
            ->whereIn('date_mouvement', array_keys($evolution->all()))
            ->get()
            ->groupBy('date_mouvement')
            ->each(function ($mouvements, $date) use (&$evolution) {
                $evolution[$date] = [
                    'entrees' => (int) $mouvements->where('typeMouvement.code', 'IN')->sum('quantite'),
                    'sorties' => (int) $mouvements->where('typeMouvement.code', 'OUT')->sum('quantite'),
                ];
            });

        $evolution = $evolution
            ->map(fn ($v, $date) => [
                'date' => $date,
                'entrees' => $v['entrees'],
                'sorties' => $v['sorties'],
            ])
            ->values();

        $stockParCategorie = Article::with('categorie')
            ->get()
            ->groupBy('categorie.nom')
            ->map(fn ($articles, $nom) => [
                'categorie' => $nom ?: 'Sans catégorie',
                'stock' => (int) $articles->sum('stock_actuel'),
            ])
            ->values();

        return response()->json([
            'totalArticles' => $totalArticles,
            'totalCategories' => $totalCategories,
            'totalEntrees' => $totalEntrees,
            'totalSorties' => $totalSorties,
            'valeurStock' => $valeurStock,
            'enRupture' => $enRupture,
            'presqueRupture' => $presqueRupture,
            'evolution' => $evolution,
'stockParCategorie' => $stockParCategorie,
        ]);
    }
}