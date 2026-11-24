<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seccion', function (Blueprint $table) {
            $table->id('sec_id');
            $table->string('sec_codigo', 20);
            $table->string('sec_sogac_id', 50)->unique()->nullable();
            $table->boolean('sec_status')->default(true);
            $table->timestamp('sec_created_at')->useCurrent();
            $table->timestamp('sec_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('sec_id_tra');
            $table->unsignedBigInteger('sec_id_tur');
            $table->unsignedBigInteger('sec_id_usu_created')->nullable();
            $table->unsignedBigInteger('sec_id_usu_updated')->nullable();
            $table->foreign('sec_id_tra')->references('tra_id')->on('trayecto');
            $table->foreign('sec_id_tur')->references('tur_id')->on('turno');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seccion');
    }
};