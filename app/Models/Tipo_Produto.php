<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tipo_Produto extends Model
{
    use HasFactory;

    protected $table = 'tipos_produtos';

    protected $fillable = [
        'nome',
        'descricao',
        'sigla',
    ];

    public function produtos(): HasMany{
        return $this->hasMany(Produto::class);
    }
}
