<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Article::with('categorie');

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('reference', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->boolean('rupture')) {
            $query->whereColumn('stock_actuel', '<=', 'stock_minimum');
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categorie_id' => 'required|exists:categories,id',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock_actuel' => 'nullable|integer|min:0',
            'stock_minimum' => 'required|integer|min:0',
            'unite' => 'required|string|max:50',
        ]);

        $validated['reference'] = $this->generateNextReference();

        $article = Article::create($validated);

        return response()->json($article->load('categorie'), 201);
    }

    
    private function generateNextReference(): string
    {
        $lastNumber = Article::where('reference', 'like', 'ART-%')
            ->get()
            ->map(fn ($a) => (int) str_replace('ART-', '', $a->reference))
            ->max() ?? 0;

        return 'ART-' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    public function nextReference(): JsonResponse
    {
        return response()->json(['reference' => $this->generateNextReference()]);
    }

    public function show(Article $article): JsonResponse
    {
        return response()->json($article->load('categorie'));
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'categorie_id' => 'nullable|exists:categories,id',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock_minimum' => 'required|integer|min:0',
            'unite' => 'required|string|max:50',
        ]);

        $article->update($validated);

        return response()->json($article->load('categorie'));
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json(['message' => 'Article supprimé avec succès']);
    }
}