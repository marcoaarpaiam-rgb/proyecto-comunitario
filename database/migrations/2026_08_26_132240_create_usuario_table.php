<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('usu_id');
            $table->string('usu_cedula', 15)->unique();
            $table->string('usu_primer_nombre', 50);
            $table->string('usu_segundo_nombre', 50)->nullable();
            $table->string('usu_primer_apellido', 50);
            $table->string('usu_segundo_apellido', 50)->nullable();
            $table->string('usu_email', 150)->unique()->nullable();
            $table->string('usu_telefono', 20)->nullable();
            $table->boolean('usu_tiene_acceso')->default(false)->comment('TRUE = puede iniciar sesión');
            $table->string('usu_username', 100)->unique()->nullable();
            $table->string('usu_password', 255)->nullable();
            $table->string('usu_sogac_id', 50)->unique()->nullable();
            $table->boolean('usu_status')->default(true);
            $table->timestamp('usu_created_at')->useCurrent();
            $table->timestamp('usu_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('usu_id_usu_created')->nullable();
            $table->unsignedBigInteger('usu_id_usu_updated')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};