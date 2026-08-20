<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = [
        'reference',
        'nom',
        'description',
        'categorie_id',
        'prix_achat',
        'prix_vente',
        'stock_actuel',
        'stock_minimum',
        'unite'
    ];
    public function categorie() : BelongsTo
    {
        return $this->belongsTo(Categorie::class,'categorie_id');
    }
    /**
     * Relation : Un article a plusieurs mouvements de stock
     */

    public function mouvementsStock() : HasMany
    {
        return $this->hasMany(MouvementStock::class );
    
}
}