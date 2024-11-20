<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fornecedor extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'fornecedores';

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
