<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo', function (Blueprint $table) {
            $table->id('equ_id');
            $table->string('equ_codigo', 20)->unique();
            $table->string('equ_titulo', 150);
            $table->boolean('equ_status')->default(true);
            $table->timestamp('equ_created_at')->useCurrent();
            $table->timestamp('equ_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('equ_id_sec');
            $table->unsignedBigInteger('equ_id_tra');
            $table->unsignedBigInteger('equ_id_usu_created')->nullable();
            $table->unsignedBigInteger('equ_id_usu_updated')->nullable();
            $table->foreign('equ_id_sec')->references('sec_id')->on('seccion');
            $table->foreign('equ_id_tra')->references('tra_id')->on('trayecto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo');
    }
};