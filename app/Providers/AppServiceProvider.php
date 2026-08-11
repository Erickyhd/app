<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Schema\Blueprint::macro('auditoria', function () {
            $this->tinyInteger('estado')->default(1)->comment('1: Activo, 0: Inactivo');
            $this->unsignedBigInteger('usuario_creacion_id')->nullable();
            $this->unsignedBigInteger('usuario_actualizacion_id')->nullable();
            $this->timestamps();
        });
    }
}
