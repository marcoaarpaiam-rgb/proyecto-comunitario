<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_proyecto', function (Blueprint $table) {
            $table->id('epr_id');
            $table->string('epr_nombre', 50);
            $table->boolean('epr_status')->default(true);
            $table->timestamp('epr_created_at')->useCurrent();
            $table->timestamp('epr_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('epr_id_usu_created')->nullable();
            $table->unsignedBigInteger('epr_id_usu_updated')->nullable();
        });

        DB::table('estado_proyecto')->insert([
            ['epr_nombre' => 'Aprobado'],
            ['epr_nombre' => 'Aplazado'],
            ['epr_nombre' => 'No Presentado'],
            ['epr_nombre' => 'No Entregado a Tiempo'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_proyecto');
    }
};