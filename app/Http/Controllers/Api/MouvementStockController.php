<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\MouvementStock;
use App\Models\TypeMouvement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MouvementStockController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MouvementStock::with(['article', 'user', 'typeMouvement']);

        if ($request->has('article_id')) {
            $query->where('article_id', $request->article_id);
        }

        if ($request->type === 'entree') {
            $query->entrees();
        } elseif ($request->type === 'sortie') {
            $query->sorties();
        }

        return response()->json($query->latest('date_mouvement')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'type_mouvement_id' => 'required|exists:type_mouvements,id',
            'quantite' => 'required|integer|min:1',
            'date_mouvement' => 'required|date',
            'motif' => 'required|string|max:150',
            'observation' => 'nullable|string',
        ]);

        $article = Article::findOrFail($validated['article_id']);
        $typeMouvement = TypeMouvement::findOrFail($validated['type_mouvement_id']);

        $estUneSortie = $typeMouvement->code === 'OUT';
        $stockActuel = $article->stock_actuel;

        // Vérification du stock pour une sortie
        if ($estUneSortie && $validated['quantite'] > $stockActuel) {
            return response()->json([
                'message' => 'Quantité insuffisante en stock.',
                'stock_disponible' => $article->stock_actuel,
            ], 422);
        }
        
    //  Calcul du stock après mouvement
    $stockAvant = $article->stock_actuel;
    $stockApres = $estUneSortie 
        ? $stockAvant - $validated['quantite'] 
        : $stockAvant + $validated['quantite'];

    //  Mise à jour du stock de l'article
    $article->stock_actuel = $stockApres;
    $article->save();

    //  Création du mouvement avec stock_apres_mouvement
    $mouvement = MouvementStock::create([
        ...$validated,
        'user_id' => auth()->id(),
        'stock_apres_mouvement' => $stockApres, 
    ]);

    $mouvement->load(['article', 'typeMouvement', 'user']);

    return response()->json($mouvement, 201);
}
}