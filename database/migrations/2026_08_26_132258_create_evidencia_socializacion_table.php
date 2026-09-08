<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencia_socializacion', function (Blueprint $table) {
            $table->id('eso_id');
            $table->string('eso_nombre_archivo', 255);
            $table->string('eso_ruta', 500);
            $table->text('eso_descripcion')->nullable();
            $table->timestamp('eso_fecha_subida')->useCurrent();
            $table->unsignedBigInteger('eso_id_soc');
            $table->unsignedBigInteger('eso_id_usu_created')->nullable();
            $table->foreign('eso_id_soc')->references('soc_id')->on('socializacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencia_socializacion');
    }
};