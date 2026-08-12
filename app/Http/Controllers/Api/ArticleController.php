<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    /**
     * GET /api/articles
     * Liste les articles, avec recherche, filtre par catégorie,
     * et filtre par niveau de stock (rupture / presque).
     */
    public function index(Request $request)
    {
        $query = Article::query()->with('categorie');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->integer('categorie_id'));
        }

        if ($request->filled('stock')) {
            match ($request->string('stock')->toString()) {
                'rupture' => $query->where('stock_actuel', '<=', 0),
                'presque' => $query->whereColumn('stock_actuel', '<=', 'stock_minimum')
                    ->where('stock_actuel', '>', 0),
                default => null,
            };
        }

        return $query->orderBy('nom')->get();
    }

    /**
     * POST /api/articles
     */
    public function store(Request $request)
    {
        $validated = $this->validateArticle($request);

        $article = Article::create($validated);
        $article->load('categorie');

        return response()->json($article, 201);
    }

    /**
     * GET /api/articles/{article}
     */
    public function show(Article $article)
    {
        $article->load('categorie');

        return $article;
    }

    /**
     * PUT /api/articles/{article}
     */
    public function update(Request $request, Article $article)
    {
        $validated = $this->validateArticle($request, $article->id);

        $article->update($validated);
        $article->load('categorie');

        return $article;
    }

    /**
     * DELETE /api/articles/{article}
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json(null, 204);
    }

    /**
     * Règles de validation communes à la création et la modification.
     * $ignoreId permet d'exclure l'article courant de la contrainte
     * d'unicité de la référence lors d'une modification.
     */
    private function validateArticle(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'reference' => [
                'required',
                'string',
                'max:255',
                Rule::unique('articles', 'reference')->ignore($ignoreId),
            ],
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'categorie_id' => ['nullable', 'exists:categories,id'],
            'prix_achat' => ['required', 'numeric', 'min:0'],
            'prix_vente' => ['required', 'numeric', 'min:0'],
            'stock_actuel' => ['required', 'integer', 'min:0'],
            'stock_minimum' => ['required', 'integer', 'min:0'],
            'unite' => ['required', 'string', 'max:50'],
        ]);
    }
}