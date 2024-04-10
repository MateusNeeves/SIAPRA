<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido_Plan extends Model
{
    use HasFactory;

    protected $table = 'pedidos_plan';

    protected $fillable = [
        'id_pedido',
        'id_planejamento',
        'ativ_dest',
        'vol_frasco',
        'qtd_doses_selec',

    ];
}
