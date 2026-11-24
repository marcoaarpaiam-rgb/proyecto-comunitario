<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('proyecto_comunidad') && !Schema::hasColumn('proyecto_comunidad', 'pco_justificacion_duplicidad')) {
            Schema::table('proyecto_comunidad', function (Blueprint $table) {
                $table->text('pco_justificacion_duplicidad')->nullable()->after('pco_resumen');
            });
        }

        if (Schema::hasTable('equipo') && !Schema::hasColumn('equipo', 'equ_justificacion_duplicidad')) {
            Schema::table('equipo', function (Blueprint $table) {
                $table->text('equ_justificacion_duplicidad')->nullable()->after('equ_titulo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('proyecto_comunidad') && Schema::hasColumn('proyecto_comunidad', 'pco_justificacion_duplicidad')) {
            Schema::table('proyecto_comunidad', function (Blueprint $table) {
                $table->dropColumn('pco_justificacion_duplicidad');
            });
        }

        if (Schema::hasTable('equipo') && Schema::hasColumn('equipo', 'equ_justificacion_duplicidad')) {
            Schema::table('equipo', function (Blueprint $table) {
                $table->dropColumn('equ_justificacion_duplicidad');
            });
        }
    }
};
