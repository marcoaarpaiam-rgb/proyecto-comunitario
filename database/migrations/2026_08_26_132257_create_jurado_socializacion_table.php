<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurado_socializacion', function (Blueprint $table) {
            $table->id('jso_id');
            $table->string('jso_nombre_externo', 100)->nullable();
            $table->boolean('jso_es_de_comunidad')->default(false);
            $table->timestamp('jso_created_at')->useCurrent();
            $table->unsignedBigInteger('jso_id_soc');
            $table->unsignedBigInteger('jso_id_usu')->nullable();
            $table->unsignedBigInteger('jso_id_tju')->nullable();
            $table->unsignedBigInteger('jso_id_usu_created')->nullable();
            $table->foreign('jso_id_soc')->references('soc_id')->on('socializacion');
            $table->foreign('jso_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('jso_id_tju')->references('tju_id')->on('tipo_jurado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurado_socializacion');
    }
};