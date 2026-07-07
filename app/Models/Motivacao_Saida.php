<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motivacao_Saida extends Model
{
    protected $table = 'motivacao_saida';

    public $timestamps = false;

    protected $fillable = [
        'motivacao',
    ];
}