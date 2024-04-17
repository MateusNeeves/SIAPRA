<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido_Frac extends Model
{
    use HasFactory;

    protected $table = 'pedidos_frac';

    protected $fillable = [
        'id_pedido',
        'id_fracionamento',
        'vol_real_frasco',
        'qtd_doses_selec',
        'ativ_dest',
        'qtd_doses_entregues',
    ];
}
