<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Pedido;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    protected $username = 'username';

    protected $fillable = [
        'username',
        'password',
        'name',
        'cpf',
        'email',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function classes(): BelongsToMany{
        return $this->belongsToMany(Classe::class, 'users_classes', 'id_user', 'id_classe');
    }

    public function getClassNamesAttribute(){
        $this->load('classes');
        return $this->classes->pluck('nome')->toArray();
    }

    protected function casts(): array{
        return [
            'password' => 'hashed',
        ];
    }
}

