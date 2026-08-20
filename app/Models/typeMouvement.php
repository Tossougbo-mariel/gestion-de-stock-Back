<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeMouvement extends Model
{
    protected $fillable = [
        'libelle',
        'code'
    ];
    public function mouvementsStock() : HasMany
    {
        return $this->hasMany(MouvementStock::class, 'type_mouvement_id');
    }
}
