<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function pedidos(): HasMany{
        return $this->hasMany(Pedido::class);
    }


    protected function casts(): array{
        return [
            'password' => 'hashed',
        ];
    }
}
