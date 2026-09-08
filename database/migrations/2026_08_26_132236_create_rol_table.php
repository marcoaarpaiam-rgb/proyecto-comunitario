<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->id('rol_id');
            $table->string('rol_nombre', 50);
            $table->string('rol_descripcion', 150)->nullable();
            $table->boolean('rol_status')->default(true);
            $table->timestamp('rol_created_at')->useCurrent();
            $table->timestamp('rol_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('rol_id_usu_created')->nullable();
            $table->unsignedBigInteger('rol_id_usu_updated')->nullable();
        });

        DB::table('rol')->insert([
            ['rol_nombre' => 'coordinador', 'rol_descripcion' => 'Coordinador del PNF, actúa como administrador'],
            ['rol_nombre' => 'profesor_proyecto', 'rol_descripcion' => 'Profesor asignado a secciones de proyecto'],
            ['rol_nombre' => 'lider', 'rol_descripcion' => 'Líder de equipo, único estudiante con acceso al sistema'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};