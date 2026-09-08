<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_organizacion', function (Blueprint $table) {
            $table->id('tor_id');
            $table->string('tor_nombre', 50);
            $table->boolean('tor_status')->default(true);
            $table->timestamp('tor_created_at')->useCurrent();
            $table->timestamp('tor_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tor_id_usu_created')->nullable();
            $table->unsignedBigInteger('tor_id_usu_updated')->nullable();
        });

        DB::table('tipo_organizacion')->insert([
            ['tor_nombre' => 'Pública'],
            ['tor_nombre' => 'Privada'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_organizacion');
    }
};