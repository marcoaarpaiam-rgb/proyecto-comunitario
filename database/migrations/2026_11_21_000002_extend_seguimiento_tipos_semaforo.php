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
        // NUEVA TABLA: tipos_encuentro_puntual
        Schema::create('tipos_encuentro_puntual', function (Blueprint $table) {
            $table->bigIncrements('tep_id');
            $table->string('tep_nombre', 150);
            $table->text('tep_descripcion')->nullable();
            $table->boolean('tep_activo')->default(true);
        });

        // Extender seguimiento_equipo con nuevas columnas
        Schema::table('seguimiento_equipo', function (Blueprint $table) {
            $table->enum('seq_estado_semaforo', ['positivo', 'neutral', 'negativo'])
                  ->nullable()
                  ->after('seq_status');
            $table->tinyInteger('seq_valoracion_avance')->unsigned()->nullable()
                  ->after('seq_estado_semaforo');
            $table->unsignedBigInteger('seq_id_tipo_encuentro')->nullable()
                  ->after('seq_valoracion_avance');

            $table->foreign('seq_id_tipo_encuentro')
                  ->references('tep_id')
                  ->on('tipos_encuentro_puntual')
                  ->onDelete('set null');
        });

        // NUEVA TABLA: acuerdos_encuentro
        Schema::create('acuerdos_encuentro', function (Blueprint $table) {
            $table->bigIncrements('acue_id');
            $table->unsignedBigInteger('acue_id_seq');
            $table->text('acue_descripcion');
            $table->enum('acue_estado', ['pendiente', 'en_proceso', 'rectificado'])->default('pendiente');
            $table->date('acue_fecha_limite')->nullable();
            $table->text('acue_observacion_cierre')->nullable();
            $table->timestamps();

            $table->foreign('acue_id_seq')
                  ->references('seq_id')
                  ->on('seguimiento_equipo')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acuerdos_encuentro');

        Schema::table('seguimiento_equipo', function (Blueprint $table) {
            $table->dropForeign(['seq_id_tipo_encuentro']);
            $table->dropColumn(['seq_estado_semaforo', 'seq_valoracion_avance', 'seq_id_tipo_encuentro']);
        });

        Schema::dropIfExists('tipos_encuentro_puntual');
    }
};
