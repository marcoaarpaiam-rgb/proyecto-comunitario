<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_notificacion', function (Blueprint $table) {
            $table->id('tno_id');
            $table->string('tno_nombre', 100);
            $table->boolean('tno_status')->default(true);
            $table->timestamp('tno_created_at')->useCurrent();
            $table->timestamp('tno_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tno_id_usu_created')->nullable();
            $table->unsignedBigInteger('tno_id_usu_updated')->nullable();
        });

        DB::table('tipo_notificacion')->insert([
            ['tno_nombre' => 'Punto de control próximo a vencer'],
            ['tno_nombre' => 'Punto de control vencido sin cumplir'],
            ['tno_nombre' => 'Punto de control cumplido fuera de plazo'],
            ['tno_nombre' => 'Plazo de entrega próximo a vencer'],
            ['tno_nombre' => 'Proyecto cerrado por vencimiento'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_notificacion');
    }
};