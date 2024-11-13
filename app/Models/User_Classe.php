<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User_Classe extends Model
{
    use HasFactory;

    protected $table = 'users_classes';

    protected $fillable = [
        'id_user',
        'id_classe'
    ];
}
