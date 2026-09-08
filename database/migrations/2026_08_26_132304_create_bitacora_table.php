<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id('bit_id');
            $table->string('bit_accion', 200);
            $table->json('bit_estado_anterior')->nullable()->comment('Estado del registro antes del cambio');
            $table->json('bit_estado_nuevo')->nullable()->comment('Estado del registro después del cambio');
            $table->unsignedBigInteger('bit_registro_id')->nullable();
            $table->text('bit_detalle')->nullable();
            $table->timestamp('bit_fecha_hora')->useCurrent();
            $table->unsignedBigInteger('bit_id_usu');
            $table->unsignedBigInteger('bit_id_entidad')->nullable();
            $table->foreign('bit_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('bit_id_entidad')->references('ent_id')->on('entidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};