<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'end_logradouro',
        'end_complemento',
        'estado',
        'cidade',
        'bairro',
        'cep',
        'tempo_transp',
    ];

    public function pedidos(): HasMany{
        return $this->hasMany(Pedido::class);
    }
}
