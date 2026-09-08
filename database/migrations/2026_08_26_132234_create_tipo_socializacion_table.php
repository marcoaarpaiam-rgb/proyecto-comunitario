<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_socializacion', function (Blueprint $table) {
            $table->id('tso_id');
            $table->string('tso_nombre', 100);
            $table->tinyInteger('tso_orden');
            $table->boolean('tso_status')->default(true);
            $table->timestamp('tso_created_at')->useCurrent();
            $table->timestamp('tso_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tso_id_usu_created')->nullable();
            $table->unsignedBigInteger('tso_id_usu_updated')->nullable();
        });

        DB::table('tipo_socializacion')->insert([
            ['tso_nombre' => 'Pre-socialización', 'tso_orden' => 1],
            ['tso_nombre' => 'Socialización Final', 'tso_orden' => 2],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_socializacion');
    }
};