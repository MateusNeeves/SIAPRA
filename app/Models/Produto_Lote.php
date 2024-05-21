<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto_Lote extends Model
{
    use HasFactory;

    protected $table = 'produtos_lote';

    protected $fillable = [
        'id_produto',
        'id_fabricante',
        'lote_fabricante',
        'id_fornecedor',
        'qtd_itens_recebidos',
        'qtd_itens_estoque',
        'preco_unitario',
        'preco_total',
        'data_entrega',
        'data_validade'
    ];
}
