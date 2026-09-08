<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('socializacion', function (Blueprint $table) {
            $table->id('soc_id');
            $table->date('soc_fecha');
            $table->time('soc_hora');
            $table->string('soc_lugar', 150);
            $table->boolean('soc_apto')->nullable()->comment('Aplica en pre-socialización');
            $table->boolean('soc_aprobado')->nullable()->comment('Aplica en socialización final');
            $table->tinyInteger('soc_cantidad_jurados')->default(0);
            $table->text('soc_observaciones_no_apto')->nullable();
            $table->text('soc_observaciones')->nullable();
            $table->boolean('soc_status')->default(true);
            $table->timestamp('soc_created_at')->useCurrent();
            $table->timestamp('soc_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('soc_id_equ');
            $table->unsignedBigInteger('soc_id_tra');
            $table->unsignedBigInteger('soc_id_tso');
            $table->unsignedBigInteger('soc_id_soc_previa')->nullable();
            $table->unsignedBigInteger('soc_id_usu_created')->nullable();
            $table->unsignedBigInteger('soc_id_usu_updated')->nullable();
            $table->foreign('soc_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('soc_id_tra')->references('tra_id')->on('trayecto');
            $table->foreign('soc_id_tso')->references('tso_id')->on('tipo_socializacion');
            $table->foreign('soc_id_soc_previa')->references('soc_id')->on('socializacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socializacion');
    }
};