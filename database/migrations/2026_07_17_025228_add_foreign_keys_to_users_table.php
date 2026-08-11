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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreign('jerarquia_id')->references('id')->on('jerarquias')->onDelete('set null');
            $table->foreign('rango_id')->references('id')->on('rangos')->onDelete('set null');
        });
        
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['jerarquia_id']);
            $table->dropForeign(['rango_id']);
        });
        
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
