<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('punto_control', function (Blueprint $table) {
            if (!Schema::hasColumn('punto_control', 'puc_es_ultimo_punto')) {
                $table->boolean('puc_es_ultimo_punto')->default(false)->after('puc_es_entregable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('punto_control', function (Blueprint $table) {
            if (Schema::hasColumn('punto_control', 'puc_es_ultimo_punto')) {
                $table->dropColumn('puc_es_ultimo_punto');
            }
        });
    }
};
