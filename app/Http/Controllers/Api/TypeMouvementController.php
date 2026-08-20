<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeMouvement;
use Illuminate\Http\JsonResponse;

class TypeMouvementController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TypeMouvement::all());
    }

    public function show(TypeMouvement $typeMouvement): JsonResponse
    {
        return response()->json($typeMouvement);
    }
}