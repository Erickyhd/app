<?php

namespace App\Domain\Shared\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
/**
 * @method static void creating(\Closure|string|array $callback)
 * @method static void updating(\Closure|string|array $callback)
 * @mixin Model
 */
trait Auditable
{
    /**
     * The "booting" method of the trait.
     *
     * @return void
     */
    protected static function bootAuditable(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->usuario_creacion_id = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->usuario_actualizacion_id = Auth::id();
            }
        });
    }
}
