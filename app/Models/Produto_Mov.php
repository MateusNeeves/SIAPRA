<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto_Mov extends Model
{
    use HasFactory;

    protected $table = 'produtos_mov';

    protected $fillable = [
        'id_produtos_lote',
        'id_destino',
        'qtd_itens_movidos',
        'hora_mov'
    ];
}
