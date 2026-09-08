<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('punto_control_jurado', function (Blueprint $table) {
            $table->id('pcj_id');
            $table->string('pcj_nombre_externo', 100)->nullable();
            $table->boolean('pcj_status')->default(true);
            $table->timestamp('pcj_created_at')->useCurrent();
            $table->unsignedBigInteger('pcj_id_puc');
            $table->unsignedBigInteger('pcj_id_usu')->nullable();
            $table->unsignedBigInteger('pcj_id_tju')->nullable();
            $table->unsignedBigInteger('pcj_id_usu_created')->nullable();
            $table->foreign('pcj_id_puc')->references('puc_id')->on('punto_control');
            $table->foreign('pcj_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('pcj_id_tju')->references('tju_id')->on('tipo_jurado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('punto_control_jurado');
    }
};