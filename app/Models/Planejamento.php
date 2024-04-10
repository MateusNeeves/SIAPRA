<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Planejamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario',
        'data_producao',
        'fator_seguranca',
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
        'duracao_ciclotron',
        'ativ_eob',
        'ativ_eos',
        'ativ_esp',
    ];
}
