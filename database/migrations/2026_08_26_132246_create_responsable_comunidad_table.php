<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responsable_comunidad', function (Blueprint $table) {
            $table->id('rco_id');
            $table->date('rco_fecha_inicio');
            $table->date('rco_fecha_fin')->nullable();
            $table->boolean('rco_status')->default(true);
            $table->timestamp('rco_created_at')->useCurrent();
            $table->unsignedBigInteger('rco_id_usu');
            $table->unsignedBigInteger('rco_id_com');
            $table->unsignedBigInteger('rco_id_usu_created')->nullable();
            $table->foreign('rco_id_usu')->references('usu_id')->on('usuario');
            $table->foreign('rco_id_com')->references('com_id')->on('comunidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsable_comunidad');
    }
};