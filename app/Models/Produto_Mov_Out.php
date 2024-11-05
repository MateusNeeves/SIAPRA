<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto_Mov_Out extends Model
{
    use HasFactory;

    protected $table = 'produtos_mov_out';

    protected $fillable = [
        'id_produtos_mov_in',
        'id_destino',
        'qtd_itens_movidos',
        'data_mov_out'
    ];
}
