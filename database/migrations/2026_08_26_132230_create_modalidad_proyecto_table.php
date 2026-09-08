<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modalidad_proyecto', function (Blueprint $table) {
            $table->id('mpr_id');
            $table->string('mpr_nombre', 100);
            $table->boolean('mpr_status')->default(true);
            $table->timestamp('mpr_created_at')->useCurrent();
            $table->timestamp('mpr_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('mpr_id_usu_created')->nullable();
            $table->unsignedBigInteger('mpr_id_usu_updated')->nullable();
        });

        DB::table('modalidad_proyecto')->insert([
            ['mpr_nombre' => 'Nuevo'],
            ['mpr_nombre' => 'Continuación'],
            ['mpr_nombre' => 'Reingeniería'],
            ['mpr_nombre' => 'Reactivación de Aplazado'],
            ['mpr_nombre' => 'Reactivación de No Presentado'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('modalidad_proyecto');
    }
};