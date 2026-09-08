<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesor_seccion', function (Blueprint $table) {
            $table->id('pse_id');
            $table->date('pse_fecha_asignacion');
            $table->date('pse_fecha_fin')->nullable();
            $table->boolean('pse_status')->default(true);
            $table->timestamp('pse_created_at')->useCurrent();
            $table->timestamp('pse_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('pse_id_usu');
            $table->unsignedBigInteger('pse_id_sec');
            $table->unsignedBigInteger('pse_id_usu_created')->nullable();
            $table->unsignedBigInteger('pse_id_usu_updated')->nullable();
            $table->foreign('pse_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('pse_id_sec')->references('sec_id')->on('seccion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesor_seccion');
    }
};