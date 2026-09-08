<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_comunidad', function (Blueprint $table) {
            $table->id('pco_id');
            $table->string('pco_resumen', 150);
            $table->date('pco_fecha_limite');
            $table->boolean('pco_entregado_a_tiempo')->nullable();
            $table->date('pco_fecha_aprobacion')->nullable();
            $table->boolean('pco_status')->default(true);
            $table->timestamp('pco_created_at')->useCurrent();
            $table->timestamp('pco_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('pco_id_equ');
            $table->unsignedBigInteger('pco_id_com');
            $table->unsignedBigInteger('pco_id_tpr');
            $table->unsignedBigInteger('pco_id_mpr');
            $table->unsignedBigInteger('pco_id_pco_previo')->nullable();
            $table->unsignedBigInteger('pco_id_usu_aprobado')->nullable();
            $table->unsignedBigInteger('pco_id_usu_created')->nullable();
            $table->unsignedBigInteger('pco_id_usu_updated')->nullable();
            $table->foreign('pco_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('pco_id_com')->references('com_id')->on('comunidad');
            $table->foreign('pco_id_tpr')->references('tpr_id')->on('tipo_proyecto');
            $table->foreign('pco_id_mpr')->references('mpr_id')->on('modalidad_proyecto');
            $table->foreign('pco_id_pco_previo')->references('pco_id')->on('proyecto_comunidad');
            $table->foreign('pco_id_usu_aprobado')->references('usu_id')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_comunidad');
    }
};