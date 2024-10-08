<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fracionamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario',
        'id_planejamento',
        'ativ_eob_real',
        'ativ_eos_nec',
        'ativ_eos_real',
        'vol_eos',
        'ativ_esp',
        'rend_sintese_real',
        'fim_sintese',
    ];
}
