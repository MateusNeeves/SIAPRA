<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto_Forn extends Model
{
    use HasFactory;

    protected $table = 'produtos_forn';
    
    public $timestamps = false;

    protected $fillable = [
        'id_produto',
        'id_fornecedor',
    ];
}
