<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyecto_beneficiario', function (Blueprint $table) {
            $table->id('pbe_id');
            $table->integer('pbe_cantidad');
            $table->timestamp('pbe_created_at')->useCurrent();
            $table->timestamp('pbe_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('pbe_id_pco');
            $table->unsignedBigInteger('pbe_id_tbe');
            $table->unsignedBigInteger('pbe_id_usu_created')->nullable();
            $table->unsignedBigInteger('pbe_id_usu_updated')->nullable();
            $table->foreign('pbe_id_pco')->references('pco_id')->on('proyecto_comunidad');
            $table->foreign('pbe_id_tbe')->references('tbe_id')->on('tipo_beneficiario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto_beneficiario');
    }
};