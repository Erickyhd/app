<?php

namespace App\Modules\Jerarquias\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Users\Domain\Models\User;

class Jerarquia extends Model
{
    protected $table = 'jerarquias';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'jerarquia_padre_id',
        'codigo',
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

    public function parent()
    {
        return $this->belongsTo(Jerarquia::class, 'jerarquia_padre_id');
    }

    public function subareas()
    {
        return $this->hasMany(Jerarquia::class, 'jerarquia_padre_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }
}
