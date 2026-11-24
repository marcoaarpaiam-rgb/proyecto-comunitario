<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencia_punto_control', function (Blueprint $table) {
            $table->id('apc_id');
            $table->boolean('apc_asistio');
            $table->text('apc_observaciones')->nullable();
            $table->timestamp('apc_created_at')->useCurrent();
            $table->unsignedBigInteger('apc_id_seq');
            $table->unsignedBigInteger('apc_id_usu');
            $table->unsignedBigInteger('apc_id_usu_created')->nullable();
            $table->foreign('apc_id_seq')->references('seq_id')->on('seguimiento_equipo');
            $table->foreign('apc_id_usu')->references('usu_id')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencia_punto_control');
    }
};