<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ampliar campos de asistencia para participación verificable y trazabilidad de edición
        if (Schema::hasTable('asistencia_punto_control')) {
            Schema::table('asistencia_punto_control', function (Blueprint $table) {
                if (!Schema::hasColumn('asistencia_punto_control', 'apc_participo')) {
                    $table->boolean('apc_participo')->default(false)->after('apc_justificado');
                }
                if (!Schema::hasColumn('asistencia_punto_control', 'apc_motivo_edicion')) {
                    $table->text('apc_motivo_edicion')->nullable()->after('apc_observaciones');
                }
                if (!Schema::hasColumn('asistencia_punto_control', 'apc_updated_at')) {
                    $table->timestamp('apc_updated_at')->nullable()->after('apc_created_at');
                }
                if (!Schema::hasColumn('asistencia_punto_control', 'apc_id_usu_updated')) {
                    $table->unsignedBigInteger('apc_id_usu_updated')->nullable()->after('apc_id_usu_created');
                    $table->foreign('apc_id_usu_updated')->references('usu_id')->on('usuario')->nullOnDelete();
                }
            });
        }

        // 2. Hacer entr_id_tet nullable en entregable para soportar entregables ligados al catálogo PNFI
        if (Schema::hasTable('entregable') && Schema::hasColumn('entregable', 'entr_id_tet')) {
            DB::statement('ALTER TABLE entregable MODIFY entr_id_tet BIGINT(20) UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('asistencia_punto_control')) {
            Schema::table('asistencia_punto_control', function (Blueprint $table) {
                if (Schema::hasColumn('asistencia_punto_control', 'apc_id_usu_updated')) {
                    $table->dropForeign(['apc_id_usu_updated']);
                    $table->dropColumn('apc_id_usu_updated');
                }
                if (Schema::hasColumn('asistencia_punto_control', 'apc_updated_at')) {
                    $table->dropColumn('apc_updated_at');
                }
                if (Schema::hasColumn('asistencia_punto_control', 'apc_motivo_edicion')) {
                    $table->dropColumn('apc_motivo_edicion');
                }
                if (Schema::hasColumn('asistencia_punto_control', 'apc_participo')) {
                    $table->dropColumn('apc_participo');
                }
            });
        }
    }
};
