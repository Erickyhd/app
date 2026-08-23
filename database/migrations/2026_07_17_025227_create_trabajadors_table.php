<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id')->nullable()->unique();
            $table->string('tipo_documento');
            $table->string('numero_documento')->unique();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero')->nullable();
            $table->string('telefono_principal');
            $table->string('direccion')->nullable();
            $table->date('fecha_contratacion');
            $table->auditoria();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
