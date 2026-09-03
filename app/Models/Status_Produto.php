<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status_Produto extends Model
{
    protected $table = 'status_produtos';

    public $timestamps = false;

    protected $fillable = [
        'status',
    ];
}