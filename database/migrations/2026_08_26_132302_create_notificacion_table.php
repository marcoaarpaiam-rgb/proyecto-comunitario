<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion', function (Blueprint $table) {
            $table->id('not_id');
            $table->text('not_mensaje');
            $table->boolean('not_leida')->default(false);
            $table->timestamp('not_fecha_generacion')->useCurrent();
            $table->timestamp('not_fecha_lectura')->nullable();
            $table->unsignedBigInteger('not_id_usu');
            $table->unsignedBigInteger('not_id_tno');
            $table->unsignedBigInteger('not_id_equ')->nullable();
            $table->unsignedBigInteger('not_id_puc')->nullable();
            $table->foreign('not_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('not_id_tno')->references('tno_id')->on('tipo_notificacion');
            $table->foreign('not_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('not_id_puc')->references('puc_id')->on('punto_control');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};