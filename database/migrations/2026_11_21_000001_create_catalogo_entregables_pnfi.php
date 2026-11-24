<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // NUEVA TABLA: catalogo_entregables_pnfi
        Schema::create('catalogo_entregables_pnfi', function (Blueprint $table) {
            $table->bigIncrements('cep_id');
            $table->unsignedBigInteger('cep_id_trayecto');
            $table->enum('cep_semestre', ['Semestre 1', 'Semestre 2']);
            $table->string('cep_nombre', 300);
            $table->text('cep_descripcion')->nullable();
            $table->tinyInteger('cep_semana_inicio_sugerida')->unsigned()->nullable();
            $table->tinyInteger('cep_semana_limite_sugerida')->unsigned()->nullable();
            $table->boolean('cep_es_obligatorio')->default(true);
            $table->tinyInteger('cep_orden')->default(1);
            $table->boolean('cep_activo')->default(true);
            $table->timestamps();

            $table->foreign('cep_id_trayecto')
                  ->references('tra_id')
                  ->on('trayecto')
                  ->onDelete('restrict');
        });

        // Agregar columna entr_id_cep a la tabla entregable existente
        Schema::table('entregable', function (Blueprint $table) {
            $table->unsignedBigInteger('entr_id_cep')->nullable()->after('entr_id_usu_created');

            $table->foreign('entr_id_cep')
                  ->references('cep_id')
                  ->on('catalogo_entregables_pnfi')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entregable', function (Blueprint $table) {
            $table->dropForeign(['entr_id_cep']);
            $table->dropColumn('entr_id_cep');
        });

        Schema::dropIfExists('catalogo_entregables_pnfi');
    }
};
