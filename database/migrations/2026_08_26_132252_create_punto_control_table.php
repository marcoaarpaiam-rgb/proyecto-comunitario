<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('punto_control', function (Blueprint $table) {
            $table->id('puc_id');
            $table->string('puc_nombre', 150);
            $table->text('puc_descripcion')->nullable();
            $table->date('puc_fecha_limite');
            $table->date('puc_fecha_reprogramada')->nullable();
            $table->text('puc_motivo_reprogramacion')->nullable();
            $table->tinyInteger('puc_dias_aviso')->default(7);
            $table->tinyInteger('puc_orden');
            $table->boolean('puc_es_entregable')->default(false);
            $table->boolean('puc_status')->default(true);
            $table->timestamp('puc_created_at')->useCurrent();
            $table->timestamp('puc_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('puc_id_tra');
            $table->unsignedBigInteger('puc_id_equ');
            $table->unsignedBigInteger('puc_id_usu_created')->nullable();
            $table->unsignedBigInteger('puc_id_usu_updated')->nullable();
            $table->foreign('puc_id_tra')->references('tra_id')->on('trayecto');
            $table->foreign('puc_id_equ')->references('equ_id')->on('equipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('punto_control');
    }
};