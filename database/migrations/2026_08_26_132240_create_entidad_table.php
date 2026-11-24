<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entidad', function (Blueprint $table) {
            $table->id('ent_id');
            $table->string('ent_nombre', 100);
            $table->string('ent_descripcion', 150)->nullable();
            $table->boolean('ent_status')->default(true);
            $table->timestamp('ent_created_at')->useCurrent();
            $table->timestamp('ent_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('ent_id_usu_created')->nullable();
            $table->unsignedBigInteger('ent_id_usu_updated')->nullable();
        });

        DB::table('entidad')->insert([
            ['ent_nombre' => 'usuario', 'ent_descripcion' => 'Usuarios del sistema'],
            ['ent_nombre' => 'equipo', 'ent_descripcion' => 'Equipos de proyecto'],
            ['ent_nombre' => 'proyecto_comunidad', 'ent_descripcion' => 'Proyectos asignados'],
            ['ent_nombre' => 'comunidad', 'ent_descripcion' => 'Catálogo de comunidades'],
            ['ent_nombre' => 'punto_control', 'ent_descripcion' => 'Puntos de control'],
            ['ent_nombre' => 'seguimiento_equipo', 'ent_descripcion' => 'Seguimiento por punto'],
            ['ent_nombre' => 'entregable', 'ent_descripcion' => 'Archivos del proyecto'],
            ['ent_nombre' => 'socializacion', 'ent_descripcion' => 'Proceso de socialización'],
            ['ent_nombre' => 'resultado_proyecto', 'ent_descripcion' => 'Resultados finales'],
            ['ent_nombre' => 'notificacion', 'ent_descripcion' => 'Alertas del sistema'],
            ['ent_nombre' => 'bitacora', 'ent_descripcion' => 'Registro de acciones'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('entidad');
    }
};