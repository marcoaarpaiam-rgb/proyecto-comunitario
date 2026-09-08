<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrante_socializacion', function (Blueprint $table) {
            $table->id('iso_id');
            $table->boolean('iso_avanza')->nullable()->comment('Pre: avanza a final. Final: fue aprobado');
            $table->text('iso_justificacion')->nullable();
            $table->timestamp('iso_created_at')->useCurrent();
            $table->unsignedBigInteger('iso_id_soc');
            $table->unsignedBigInteger('iso_id_usu');
            $table->unsignedBigInteger('iso_id_usu_created')->nullable();
            $table->foreign('iso_id_soc')->references('soc_id')->on('socializacion');
            $table->foreign('iso_id_usu')->references('usu_id')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrante_socializacion');
    }
};