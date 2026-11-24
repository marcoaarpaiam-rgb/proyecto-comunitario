<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turno', function (Blueprint $table) {
            $table->id('tur_id');
            $table->string('tur_nombre', 50);
            $table->boolean('tur_status')->default(true);
            $table->timestamp('tur_created_at')->useCurrent();
            $table->timestamp('tur_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tur_id_usu_created')->nullable();
            $table->unsignedBigInteger('tur_id_usu_updated')->nullable();
        });

        DB::table('turno')->insert([
            ['tur_nombre' => 'Mañana'],
            ['tur_nombre' => 'Tarde'],
            ['tur_nombre' => 'Noche'],
            ['tur_nombre' => 'Fin de Semana'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('turno');
    }
};