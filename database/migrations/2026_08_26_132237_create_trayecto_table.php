<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trayecto', function (Blueprint $table) {
            $table->id('tra_id');
            $table->tinyInteger('tra_numero');
            $table->string('tra_nombre', 50);
            $table->boolean('tra_status')->default(true);
            $table->timestamp('tra_created_at')->useCurrent();
            $table->timestamp('tra_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tra_id_pnf')->nullable();
            $table->unsignedBigInteger('tra_id_usu_created')->nullable();
            $table->unsignedBigInteger('tra_id_usu_updated')->nullable();
            $table->foreign('tra_id_pnf')->references('pnf_id')->on('pnf');
        });

        DB::table('trayecto')->insert([
            ['tra_id_pnf' => 1, 'tra_numero' => 1, 'tra_nombre' => 'Trayecto I'],
            ['tra_id_pnf' => 1, 'tra_numero' => 2, 'tra_nombre' => 'Trayecto II'],
            ['tra_id_pnf' => 1, 'tra_numero' => 3, 'tra_nombre' => 'Trayecto III'],
            ['tra_id_pnf' => 1, 'tra_numero' => 4, 'tra_nombre' => 'Trayecto IV'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('trayecto');
    }
};