<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $lineas = [
            'Alfabetización Tecnológica',
            'Desarrollo de Software',
        ];

        foreach ($lineas as $nombre) {
            $existe = DB::table('tipo_proyecto')
                ->whereRaw('LOWER(TRIM(tpr_nombre)) = ?', [mb_strtolower(trim($nombre), 'UTF-8')])
                ->exists();

            if (!$existe) {
                DB::table('tipo_proyecto')->insert([
                    'tpr_nombre'     => $nombre,
                    'tpr_status'     => true,
                    'tpr_created_at' => now(),
                    'tpr_updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No destructivo
    }
};
