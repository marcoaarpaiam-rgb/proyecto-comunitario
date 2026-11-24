<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('usu_firma_digital', 500)->nullable()
                ->after('usu_sogac_id')
                ->comment('Ruta de la firma digital del coordinador');
        });
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('usu_firma_digital');
        });
    }
};