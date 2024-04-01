<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fracionamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario',
        'data_producao',
        'ativ_eob_calc',
        'ativ_eob_real',
        'fim_sintese',
        'ativ_eos_nec',
        'ativ_eos_real',
        'vol_eos',
        'ativ_esp',
        'rend_sintese_real',
    ];
}
