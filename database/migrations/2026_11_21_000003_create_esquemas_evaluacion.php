<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esquemas_evaluacion', function (Blueprint $table) {
            $table->bigIncrements('esq_id');
            $table->string('esq_nombre', 200);
            $table->text('esq_descripcion')->nullable();
            $table->unsignedBigInteger('esq_id_usu_creador');
            $table->enum('esq_modo', ['cuantitativo', 'cualitativo_respaldado'])->default('cuantitativo');
            $table->decimal('esq_escala_min', 5, 2)->default(1.00);
            $table->decimal('esq_escala_max', 5, 2)->default(20.00);
            $table->json('esq_criterios')->nullable();
            $table->decimal('esq_nota_aprobatoria', 5, 2)->default(16.00);
            $table->boolean('esq_activo')->default(true);
            $table->boolean('esq_es_plantilla')->default(false);
            $table->timestamps();

            $table->foreign('esq_id_usu_creador')
                  ->references('usu_id')
                  ->on('usuario')
                  ->onDelete('restrict');
        });

        Schema::create('esquema_seccion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('esq_sec_id_esq');
            $table->unsignedBigInteger('esq_sec_id_sec');
            $table->string('esq_sec_lapso', 20)->nullable();
            $table->boolean('esq_sec_activo')->default(true);
            $table->timestamps();

            $table->foreign('esq_sec_id_esq')
                  ->references('esq_id')
                  ->on('esquemas_evaluacion')
                  ->onDelete('cascade');

            $table->foreign('esq_sec_id_sec')
                  ->references('sec_id')
                  ->on('seccion')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esquema_seccion');
        Schema::dropIfExists('esquemas_evaluacion');
    }
};
