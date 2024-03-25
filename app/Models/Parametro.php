<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
    use HasFactory;

    protected $fillable = [
        'ativ_dose',
        'tempo_exames',
        'vol_max_cq',
        'tempo_exped',
        'rend_tip_ciclotron',
        'corrente_alvo',
        'rend_sintese',
        'tempo_sintese',
        'vol_eos',
        'hora_saida',
    ];
}
