<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_equipo', function (Blueprint $table) {
            $table->id('heq_id');
            $table->text('heq_motivo')->nullable();
            $table->date('heq_fecha_evento');
            $table->timestamp('heq_created_at')->useCurrent();
            $table->unsignedBigInteger('heq_id_equ');
            $table->unsignedBigInteger('heq_id_tee');
            $table->unsignedBigInteger('heq_id_usu_created')->nullable();
            $table->foreign('heq_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('heq_id_tee')->references('tee_id')->on('tipo_evento_equipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_equipo');
    }
};