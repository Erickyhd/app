<?php

namespace App\Modules\Rangos\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Users\Domain\Models\User;

class Rango extends Model
{
    protected $table = 'rangos';
    
    protected $fillable = [
        'nombre',
        'nivel',
        'descripcion',
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

    public function creador()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }
}
