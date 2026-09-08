<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_proyecto', function (Blueprint $table) {
            $table->id('tpr_id');
            $table->string('tpr_nombre', 100);
            $table->boolean('tpr_status')->default(true);
            $table->timestamp('tpr_created_at')->useCurrent();
            $table->timestamp('tpr_updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('tpr_id_usu_created')->nullable();
            $table->unsignedBigInteger('tpr_id_usu_updated')->nullable();
        });

        DB::table('tipo_proyecto')->insert([
            ['tpr_nombre' => 'Control de Estudios'],
            ['tpr_nombre' => 'Biblioteca'],
            ['tpr_nombre' => 'Inventario'],
            ['tpr_nombre' => 'Sistema Administrativo'],
            ['tpr_nombre' => 'Otro'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_proyecto');
    }
};