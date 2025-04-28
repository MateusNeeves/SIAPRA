<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidade_Medida extends Model
{
    protected $table = 'unidades_medida';
    
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'sigla'
    ];
}
