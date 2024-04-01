<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido_Frac extends Model
{
    use HasFactory;

    protected $table = 'pedidos_frac';

    protected $fillable = [
        'id_pedido',
        'id_fracionamento',
        'vol_real_frasco'
    ];
}
