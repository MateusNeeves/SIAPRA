<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto_Fab extends Model
{
    use HasFactory;

    protected $table = 'produtos_fab';

    protected $fillable = [
        'id_produto',
        'id_fabricante',
    ];
}
