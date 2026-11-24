<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;

class EsquemasEvaluacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $primerUsuario = Usuario::first();
        $idCreador = $primerUsuario ? $primerUsuario->usu_id : 1;

        $plantillas = [
            [
                'esq_nombre'          => 'Evaluación Cuantitativa PST UPTP',
                'esq_descripcion'     => 'Esquema cuantitativo estándar de 1 a 20 puntos con escala oficial UPTP para proyectos socio-tecnológicos.',
                'esq_id_usu_creador'  => $idCreador,
                'esq_modo'            => 'cuantitativo',
                'esq_escala_min'      => 1.00,
                'esq_escala_max'      => 20.00,
                'esq_criterios'       => json_encode([
                    ['nombre' => 'Contenido del informe técnico', 'peso' => 30],
                    ['nombre' => 'Metodología aplicada y rigor', 'peso' => 25],
                    ['nombre' => 'Impacto comunitario y pertinencia', 'peso' => 25],
                    ['nombre' => 'Dominio y presentación / socialización', 'peso' => 20],
                ]),
                'esq_nota_aprobatoria'=> 16.00,
                'esq_activo'          => true,
                'esq_es_plantilla'    => true,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'esq_nombre'          => 'Evaluación Cualitativa con Respaldo Documental',
                'esq_descripcion'     => 'Esquema cualitativo con verificación de evidencias, actas de la comunidad y coherencia metodológica.',
                'esq_id_usu_creador'  => $idCreador,
                'esq_modo'            => 'cualitativo_respaldado',
                'esq_escala_min'      => 1.00,
                'esq_escala_max'      => 20.00,
                'esq_criterios'       => json_encode([
                    ['nombre' => 'Diagnóstico comunitario participativo documentado', 'peso' => 25],
                    ['nombre' => 'Coherencia metodológica y desarrollo técnico', 'peso' => 25],
                    ['nombre' => 'Evidencias de participación y actas firmadas', 'peso' => 25],
                    ['nombre' => 'Pertinencia social y transferencia de conocimiento', 'peso' => 25],
                ]),
                'esq_nota_aprobatoria'=> 16.00,
                'esq_activo'          => true,
                'esq_es_plantilla'    => true,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ];

        foreach ($plantillas as $plantilla) {
            DB::table('esquemas_evaluacion')->updateOrInsert(
                ['esq_nombre' => $plantilla['esq_nombre']],
                $plantilla
            );
        }
    }
}
