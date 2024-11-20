<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Dest_Produto extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'dest_produtos';

    protected $fillable = [
        'nome',
    ];

    public function produtos(): HasMany{
        return $this->hasMany(Produto::class);
    }
}
