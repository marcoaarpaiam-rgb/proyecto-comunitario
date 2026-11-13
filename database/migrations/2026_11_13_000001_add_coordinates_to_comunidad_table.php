<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comunidad', function (Blueprint $table) {
            $table->decimal('com_lat', 10, 7)->nullable()->after('com_ubicacion');
            $table->decimal('com_lng', 10, 7)->nullable()->after('com_lat');
        });
    }

    public function down(): void
    {
        Schema::table('comunidad', function (Blueprint $table) {
            $table->dropColumn(['com_lat', 'com_lng']);
        });
    }
};
