<?php

namespace App\Modules\Users\Infrastructure\Persistence;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Domain\Shared\Traits\Auditable;

class UserModel extends Authenticatable
{
    use HasApiTokens, Notifiable, Auditable;

    protected $table = 'usuarios'; // Tabla en español como tienes en tu config

    protected $fillable = [
        'nombres',
        'correo',
        'clave',
    ];

    protected $hidden = [
        'clave',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->clave;
    }

    public function getAuthIdentifierName()
    {
        return 'id'; // or 'correo' if that's the identifier, but id is default
    }
}
