<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_entregable_trayecto', function (Blueprint $table) {
            $table->id('tet_id');
            $table->string('tet_nombre', 150);
            $table->tinyInteger('tet_trayecto_numero');
            $table->boolean('tet_status')->default(true);
            $table->timestamp('tet_created_at')->useCurrent();
            $table->timestamp('tet_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tet_id_usu_created')->nullable();
            $table->unsignedBigInteger('tet_id_usu_updated')->nullable();
        });

        DB::table('tipo_entregable_trayecto')->insert([
            ['tet_nombre' => 'Guía de Alfabetización Tecnológica', 'tet_trayecto_numero' => 1],
            ['tet_nombre' => 'Guía de Arquitectura', 'tet_trayecto_numero' => 1],
            ['tet_nombre' => 'Manual de Sistema', 'tet_trayecto_numero' => 2],
            ['tet_nombre' => 'Manual de Usuario', 'tet_trayecto_numero' => 2],
            ['tet_nombre' => 'Manual de Instalación', 'tet_trayecto_numero' => 2],
            ['tet_nombre' => 'Manual de Sistema', 'tet_trayecto_numero' => 3],
            ['tet_nombre' => 'Manual de Usuario', 'tet_trayecto_numero' => 3],
            ['tet_nombre' => 'Manual de Instalación', 'tet_trayecto_numero' => 3],
            ['tet_nombre' => 'Manual de Usuario', 'tet_trayecto_numero' => 4],
            ['tet_nombre' => 'Manual de Sistema', 'tet_trayecto_numero' => 4],
            ['tet_nombre' => 'Manual de Instalación', 'tet_trayecto_numero' => 4],
            ['tet_nombre' => 'Normas y Procedimientos', 'tet_trayecto_numero' => 4],
            ['tet_nombre' => 'Políticas y Estándares de Seguridad', 'tet_trayecto_numero' => 4],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_entregable_trayecto');
    }
};