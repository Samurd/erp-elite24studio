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
        Schema::table('permissions', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->constrained('areas')->onDelete('cascade');
            $table->index('area_id');
            $table->string('action');

            $table->dropUnique('permissions_name_guard_name_unique');

            // 🔹 Creamos el nuevo índice único compuesto
            $table->unique(['name', 'guard_name', 'area_id'], 'permissions_name_guard_name_area_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Revertimos los cambios
            $table->dropUnique('permissions_name_guard_name_area_id_unique');
            $table->unique(['name', 'guard_name'], 'permissions_name_guard_name_unique');

            $table->dropForeign(['area_id']);
            $table->dropIndex(['area_id']);
            $table->dropColumn('area_id');
            $table->dropColumn('action');
        });
    }
};
