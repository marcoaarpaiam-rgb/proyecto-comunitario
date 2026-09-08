<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunidad', function (Blueprint $table) {
            $table->id('com_id');
            $table->string('com_nombre', 150);
            $table->string('com_ubicacion', 255);
            $table->boolean('com_status')->default(true);
            $table->timestamp('com_created_at')->useCurrent();
            $table->timestamp('com_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('com_id_tor');
            $table->unsignedBigInteger('com_id_usu_created')->nullable();
            $table->unsignedBigInteger('com_id_usu_updated')->nullable();
            $table->foreign('com_id_tor')->references('tor_id')->on('tipo_organizacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunidad');
    }
};