<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategorieController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Categorie::query();

        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|min:3|max:150|unique:categories,nom|regex:/^(?=.*\pL)[\pL\d\s\'-]+$/u',
        ]);

        $categorie = Categorie::create($validated);

        return response()->json($categorie, 201);
    }

    public function show(Categorie $categorie): JsonResponse
    {
        return response()->json($categorie);
    }

    public function update(Request $request, Categorie $categorie): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|min:3|max:150|unique:categories,nom,' . $categorie->id,
        ]);

        $categorie->update($validated);

        return response()->json($categorie);
    }

    public function destroy(Categorie $categorie): JsonResponse
    {
         if ($categorie->articles()->exists()) {
        return response()->json([
            'message' => 'Impossible de supprimer cette catégorie. Des articles sont associés à cette catégorie.',
        ], 422);
    }
        $categorie->delete();

        return response()->json(['message' => 'Catégorie supprimée avec succès']);
    }
}

    