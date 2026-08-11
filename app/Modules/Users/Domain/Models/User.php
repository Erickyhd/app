<?php

namespace App\Modules\Users\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;
use App\Modules\Jerarquias\Domain\Models\Jerarquia;
use App\Modules\Rangos\Domain\Models\Rango;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'usuarios';

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'email',
        'password',
        'telefono',
        'genero',
        'foto',
        'ultimo_login_at',
        'jerarquia_id',
        'rango_id',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ultimo_login_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::addGlobalScope('activo', function (Builder $builder) {
            $builder->where('estado', 1);
        });
    }

    public function jerarquia()
    {
        return $this->belongsTo(Jerarquia::class, 'jerarquia_id');
    }

    public function rango()
    {
        return $this->belongsTo(Rango::class, 'rango_id');
    }
}
