<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_jurado', function (Blueprint $table) {
            $table->id('tuj_id');
            $table->string('tuj_nombre_externo', 100)->nullable();
            $table->string('tuj_rol', 50)->comment('asesor | jurado');
            $table->boolean('tuj_status')->default(true);
            $table->timestamp('tuj_created_at')->useCurrent();
            $table->timestamp('tuj_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tuj_id_pco');
            $table->unsignedBigInteger('tuj_id_usu')->nullable();
            $table->unsignedBigInteger('tuj_id_tju')->nullable();
            $table->unsignedBigInteger('tuj_id_usu_created')->nullable();
            $table->unsignedBigInteger('tuj_id_usu_updated')->nullable();
            $table->foreign('tuj_id_pco')->references('pco_id')->on('proyecto_comunidad');
            $table->foreign('tuj_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('tuj_id_tju')->references('tju_id')->on('tipo_jurado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_jurado');
    }
};