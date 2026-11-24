<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carta_presentacion', function (Blueprint $table) {
            $table->boolean('cpr_solicitada')->default(false)
                ->after('cpr_id_pco');
            $table->timestamp('cpr_fecha_aprobacion')->nullable()
                ->after('cpr_sello');
        });
    }

    public function down(): void
    {
        Schema::table('carta_presentacion', function (Blueprint $table) {
            $table->dropColumn(['cpr_solicitada', 'cpr_fecha_aprobacion']);
        });
    }
};