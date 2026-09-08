<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_rol', function (Blueprint $table) {
            $table->id('uro_id');
            $table->date('uro_fecha_inicio');
            $table->date('uro_fecha_fin')->nullable();
            $table->boolean('uro_status')->default(true);
            $table->timestamp('uro_created_at')->useCurrent();
            $table->unsignedBigInteger('uro_id_usu');
            $table->unsignedBigInteger('uro_id_rol');
            $table->unsignedBigInteger('uro_id_usu_created')->nullable();
            $table->foreign('uro_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('uro_id_rol')->references('rol_id')->on('rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_rol');
    }
};