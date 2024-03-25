<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'qtd_doses',
        'data_solicitacao',
        'data_entrega',
    ];


    public function clientes(): BelongsTo{
        return $this->belongsTo(Cliente::class);
    }

    public function users(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
