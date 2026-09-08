<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregable', function (Blueprint $table) {
            $table->id('entr_id');
            $table->string('entr_nombre_archivo', 255);
            $table->string('entr_ruta', 500);
            $table->string('entr_version', 20)->nullable();
            $table->boolean('entr_es_entregable_final')->default(false);
            $table->boolean('entr_a_tiempo')->nullable();
            $table->boolean('entr_aprobado')->nullable()->comment('NULL=pendiente TRUE=aprobado FALSE=rechazado');
            $table->timestamp('entr_fecha_aprobacion')->nullable();
            $table->text('entr_observacion_rechazo')->nullable();
            $table->timestamp('entr_fecha_subida')->useCurrent();
            $table->boolean('entr_status')->default(true);
            $table->unsignedBigInteger('entr_id_equ');
            $table->unsignedBigInteger('entr_id_tet');
            $table->unsignedBigInteger('entr_id_puc')->nullable();
            $table->unsignedBigInteger('entr_id_soc')->nullable();
            $table->unsignedBigInteger('entr_id_usu_aprobado')->nullable();
            $table->unsignedBigInteger('entr_id_usu_created')->nullable();
            $table->foreign('entr_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('entr_id_tet')->references('tet_id')->on('tipo_entregable_trayecto');
            $table->foreign('entr_id_puc')->references('puc_id')->on('punto_control');
            $table->foreign('entr_id_soc')->references('soc_id')->on('socializacion');
            $table->foreign('entr_id_usu_aprobado')->references('usu_id')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregable');
    }
};