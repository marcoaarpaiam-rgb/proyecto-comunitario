<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultado_proyecto', function (Blueprint $table) {
            $table->id('rpr_id');
            $table->boolean('rpr_habilitado_tardio')->default(false);
            $table->text('rpr_observaciones')->nullable();
            $table->date('rpr_fecha_registro');
            $table->boolean('rpr_status')->default(true);
            $table->timestamp('rpr_created_at')->useCurrent();
            $table->timestamp('rpr_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('rpr_id_pco');
            $table->unsignedBigInteger('rpr_id_equ');
            $table->unsignedBigInteger('rpr_id_tra');
            $table->unsignedBigInteger('rpr_id_epr');
            $table->unsignedBigInteger('rpr_id_usu_created')->nullable();
            $table->unsignedBigInteger('rpr_id_usu_updated')->nullable();
            $table->foreign('rpr_id_pco')->references('pco_id')->on('proyecto_comunidad');
            $table->foreign('rpr_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('rpr_id_tra')->references('tra_id')->on('trayecto');
            $table->foreign('rpr_id_epr')->references('epr_id')->on('estado_proyecto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultado_proyecto');
    }
};