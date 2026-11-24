<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_jurado', function (Blueprint $table) {
            $table->id('tju_id');
            $table->string('tju_nombre', 100);
            $table->boolean('tju_status')->default(true);
            $table->timestamp('tju_created_at')->useCurrent();
            $table->timestamp('tju_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tju_id_usu_created')->nullable();
            $table->unsignedBigInteger('tju_id_usu_updated')->nullable();
        });

        DB::table('tipo_jurado')->insert([
            ['tju_nombre' => 'Invitado'],
            ['tju_nombre' => 'Institucional'],
            ['tju_nombre' => 'De Comunidad'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_jurado');
    }
};