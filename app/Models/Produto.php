<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produto extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'id_tipo',
        'data_emissao',
        'qtd_aceitavel',
        'qtd_minima',
        'quarentena',
        'epm',
        'id_unidade_medida'
    ];

    public function tipos_clientes(): BelongsTo{
        return $this->belongsTo(Tipo_Produto::class);
    }

}
