<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido_Plan extends Model
{
    use HasFactory;

    protected $table = 'pedidos_plan';

    protected $fillable = [
        'id_pedido',
        'id_planejamento',
        'ativ_dest',
        'vol_frasco',
    ];
}
