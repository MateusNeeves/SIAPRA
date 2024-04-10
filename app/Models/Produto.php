<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produto extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'descricao',
        'id_tipo',
        'data_emissao',
        'qtd_aceitavel',
        'qtd_minima',
    ];

    public function tipos_clientes(): BelongsTo{
        return $this->belongsTo(Tipo_Produto::class);
    }

}
