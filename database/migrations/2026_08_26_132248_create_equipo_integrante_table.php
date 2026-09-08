<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo_integrante', function (Blueprint $table) {
            $table->id('ein_id');
            $table->boolean('ein_es_lider')->default(false);
            $table->date('ein_fecha_ingreso');
            $table->date('ein_fecha_salida')->nullable();
            $table->boolean('ein_status')->default(true);
            $table->timestamp('ein_created_at')->useCurrent();
            $table->timestamp('ein_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('ein_id_equ');
            $table->unsignedBigInteger('ein_id_usu');
            $table->unsignedBigInteger('ein_id_usu_created')->nullable();
            $table->unsignedBigInteger('ein_id_usu_updated')->nullable();
            $table->foreign('ein_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('ein_id_usu')->references('usu_id')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_integrante');
    }
};