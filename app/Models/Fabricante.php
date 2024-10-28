<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fabricante extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'pais',
        'cnpj',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'cidade',
        'estado',
        'nome_contato',
        'email',
        'site',
    ];
}
