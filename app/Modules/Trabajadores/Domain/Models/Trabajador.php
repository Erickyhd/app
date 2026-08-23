<?php

namespace App\Modules\Trabajadores\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Users\Domain\Models\User;

class Trabajador extends Model
{
    protected $table = 'trabajadores';
    
    protected $fillable = [
        'usuario_id',
        'tipo_documento',
        'numero_documento',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'telefono_principal',
        'direccion',
        'fecha_contratacion',
        'estado',
        'usuario_creacion_id',
        'usuario_actualizacion_id'
    ];

    protected static function booted()
    {
        static::addGlobalScope('activo', function (Builder $builder) {
            $builder->where('estado', 1);
        });
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }
}
