<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Barryvdh\DomPDF\Facade\Pdf;

class ArticleExportController extends Controller
{
    public function pdf()
    {
        $articles = Article::with('categorie')->orderBy('reference')->get();

        $pdf = Pdf::loadView('articles.pdf', compact('articles'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('RAPPORT_ARTICLES_' . now()->format('Y-m-d') . '.pdf');
    }
}
