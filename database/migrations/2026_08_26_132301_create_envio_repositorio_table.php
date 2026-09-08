<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envio_repositorio', function (Blueprint $table) {
            $table->id('enr_id');
            $table->string('enr_estado_envio', 20)->comment('pendiente | enviado | error');
            $table->text('enr_respuesta')->nullable();
            $table->timestamp('enr_fecha_envio')->nullable();
            $table->timestamp('enr_created_at')->useCurrent();
            $table->unsignedBigInteger('enr_id_equ');
            $table->unsignedBigInteger('enr_id_rpr');
            $table->unsignedBigInteger('enr_id_usu_created')->nullable();
            $table->foreign('enr_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('enr_id_rpr')->references('rpr_id')->on('resultado_proyecto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envio_repositorio');
    }
};