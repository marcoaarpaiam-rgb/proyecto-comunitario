<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_evento_equipo', function (Blueprint $table) {
            $table->id('tee_id');
            $table->string('tee_nombre', 50);
            $table->boolean('tee_status')->default(true);
            $table->timestamp('tee_created_at')->useCurrent();
            $table->timestamp('tee_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tee_id_usu_created')->nullable();
            $table->unsignedBigInteger('tee_id_usu_updated')->nullable();
        });

        DB::table('tipo_evento_equipo')->insert([
            ['tee_nombre' => 'Disuelto'],
            ['tee_nombre' => 'Reactivado'],
            ['tee_nombre' => 'Modificado'],
            ['tee_nombre' => 'Integrante Agregado'],
            ['tee_nombre' => 'Integrante Removido'],
            ['tee_nombre' => 'Líder Cambiado'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_evento_equipo');
    }
};