<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model  
{ 
    protected $table = 'mouvement_stocks';

    protected $fillable = [
        'article_id',
        'user_id',
        'type_mouvement_id',
        'quantite',
        'date_mouvement',
        'motif',
        'observation',
        'stock_apres_mouvement'
    ];

    public function article()
    { 
        return $this->belongsTo(Article::class); 
    } 
 
    public function user() { 
        return $this->belongsTo(User::class); 
    } 
 
    public function typeMouvement() { 
        return $this->belongsTo(TypeMouvement::class); 
    }
    
      public function scopeEntrees(Builder $query): Builder
    {
        return $query->whereHas('typeMouvement', function ($q) {
            $q->where('code', 'IN');
        });
    }

    public function scopeSorties(Builder $query): Builder
    {
        return $query->whereHas('typeMouvement', function ($q) {
            $q->where('code', 'OUT');
        });
    }
}

