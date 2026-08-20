<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\ArticleExportController;
use App\Http\Controllers\Api\MouvementStockController;
use App\Http\Controllers\Api\TypeMouvementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    return response()->json([
        'message' => '✅ Backend StockFlow fonctionne !',
        'status' => 'success',
        'db' => '✅ Connecté à MySQL'
    ]);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    
    Route::apiResource('categories', CategorieController::class)
    ->parameters(['categories' => 'categorie']);
    Route::get('/articles/next-reference', [ArticleController::class, 'nextReference']);
    Route::get('/articles/export/pdf', [ArticleExportController::class, 'pdf']);
    Route::apiResource('articles', ArticleController::class);
    Route::get('/dashboard', [DashboardController::class, 'stats']);

    Route::get('/mouvements', [MouvementStockController::class, 'index']);
    Route::post('/mouvements', [MouvementStockController::class, 'store']);
    Route::get('/mouvements/{mouvementStock}', [MouvementStockController::class, 'show']);

    Route::get('/type-mouvements', [TypeMouvementController::class, 'index']);
    Route::get('/type-mouvements/{typeMouvement}', [TypeMouvementController::class, 'show']);
});