<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto_Mov_In extends Model
{
    use HasFactory;

    protected $table = 'produtos_mov_in';
    
    public $timestamps = false;

    protected $fillable = [
        'id_produto',
        'id_fabricante',
        'lote_fabricante',
        'id_fornecedor',
        'qtd_itens_recebidos',
        'qtd_itens_estoque',
        'preco',
        'data_entrega',
        'data_validade',
        'quarentena',
    ];
}
