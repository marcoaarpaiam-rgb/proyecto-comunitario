<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiante_seccion', function (Blueprint $table) {
            $table->id('ese_id');
            $table->date('ese_fecha_asignacion');
            $table->date('ese_fecha_fin')->nullable();
            $table->boolean('ese_status')->default(true);
            $table->timestamp('ese_created_at')->useCurrent();
            $table->timestamp('ese_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('ese_id_usu');
            $table->unsignedBigInteger('ese_id_sec');
            $table->unsignedBigInteger('ese_id_usu_created')->nullable();
            $table->unsignedBigInteger('ese_id_usu_updated')->nullable();
            $table->foreign('ese_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('ese_id_sec')->references('sec_id')->on('seccion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiante_seccion');
    }
};