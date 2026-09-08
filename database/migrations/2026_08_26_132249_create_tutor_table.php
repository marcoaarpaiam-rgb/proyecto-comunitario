<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor', function (Blueprint $table) {
            $table->id('tut_id');
            $table->boolean('tut_status')->default(true);
            $table->timestamp('tut_created_at')->useCurrent();
            $table->timestamp('tut_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tut_id_equ');
            $table->unsignedBigInteger('tut_id_usu');
            $table->unsignedBigInteger('tut_id_usu_created')->nullable();
            $table->unsignedBigInteger('tut_id_usu_updated')->nullable();
            $table->foreign('tut_id_equ')->references('equ_id')->on('equipo');
            $table->foreign('tut_id_usu')->references('usu_id')->on('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor');
    }
};