<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persona_cargo', function (Blueprint $table) {
            $table->id('pca_id');
            $table->string('pca_cargo', 100);
            $table->date('pca_fecha_inicio');
            $table->date('pca_fecha_fin')->nullable();
            $table->boolean('pca_status')->default(true);
            $table->timestamp('pca_created_at')->useCurrent();
            $table->unsignedBigInteger('pca_id_usu');
            $table->unsignedBigInteger('pca_id_usu_created')->nullable();
            $table->foreign('pca_id_usu')->references('usu_id')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona_cargo');
    }
};