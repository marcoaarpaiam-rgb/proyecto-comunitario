<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnf', function (Blueprint $table) {
            $table->id('pnf_id');
            $table->string('pnf_nombre', 100);
            $table->string('pnf_siglas', 10)->nullable();
            $table->boolean('pnf_status')->default(true);
            $table->timestamp('pnf_created_at')->useCurrent();
            $table->timestamp('pnf_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('pnf_id_usu_created')->nullable();
            $table->unsignedBigInteger('pnf_id_usu_updated')->nullable();
        });

        DB::table('pnf')->insert([
            ['pnf_nombre' => 'Informática', 'pnf_siglas' => 'INF'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pnf');
    }
};