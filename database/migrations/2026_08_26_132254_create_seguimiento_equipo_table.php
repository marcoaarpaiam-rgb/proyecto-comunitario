<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimiento_equipo', function (Blueprint $table) {
            $table->id('seq_id');
            $table->boolean('seq_cumplido')->default(false);
            $table->date('seq_fecha_cumplimiento')->nullable();
            $table->boolean('seq_cumplido_a_tiempo')->nullable();
            $table->text('seq_observaciones')->nullable();
            $table->boolean('seq_es_previa_socializacion')->default(false);
            $table->date('seq_fecha_registro');
            $table->boolean('seq_status')->default(true);
            $table->timestamp('seq_created_at')->useCurrent();
            $table->timestamp('seq_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('seq_id_equ');
            $table->unsignedBigInteger('seq_id_puc');
            $table->unsignedBigInteger('seq_id_usu_created')->nullable();
            $table->unsignedBigInteger('seq_id_usu_updated')->nullable();
            $table->foreign('seq_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('seq_id_puc')->references('puc_id')->on('punto_control');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimiento_equipo');
    }
};