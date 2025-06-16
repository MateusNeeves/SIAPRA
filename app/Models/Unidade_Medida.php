<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidade_Medida extends Model
{
    protected $table = 'unidade_medida';
    
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'sigla'
    ];
}
