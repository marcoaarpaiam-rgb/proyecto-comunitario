<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carta_presentacion', function (Blueprint $table) {
            $table->id('cpr_id');
            $table->string('cpr_ruta_archivo', 500);
            $table->string('cpr_firma_digital', 255)->nullable();
            $table->boolean('cpr_sello')->default(false);
            $table->timestamp('cpr_fecha_generacion')->useCurrent();
            $table->unsignedBigInteger('cpr_id_pco');
            $table->unsignedBigInteger('cpr_id_usu_created')->nullable();
            $table->foreign('cpr_id_pco')->references('pco_id')->on('proyecto_comunidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carta_presentacion');
    }
};