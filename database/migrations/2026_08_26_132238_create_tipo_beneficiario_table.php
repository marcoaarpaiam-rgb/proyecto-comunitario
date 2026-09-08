<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_beneficiario', function (Blueprint $table) {
            $table->id('tbe_id');
            $table->string('tbe_nombre', 100);
            $table->boolean('tbe_status')->default(true);
            $table->timestamp('tbe_created_at')->useCurrent();
            $table->timestamp('tbe_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tbe_id_usu_created')->nullable();
            $table->unsignedBigInteger('tbe_id_usu_updated')->nullable();
        });

        DB::table('tipo_beneficiario')->insert([
            ['tbe_nombre' => 'Estudiantes'],
            ['tbe_nombre' => 'Profesores'],
            ['tbe_nombre' => 'Personal Administrativo'],
            ['tbe_nombre' => 'Comunidad en General'],
            ['tbe_nombre' => 'Instituciones'],
            ['tbe_nombre' => 'Otro'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_beneficiario');
    }
};