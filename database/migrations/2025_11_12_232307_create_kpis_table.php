<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_code')->nullable(); // Ej: 01, 02A...
            $table->string('indicator_name'); // Ej: "Gestión de Trámites y Licencias"
            $table->integer('periodicity_days')->default(30); // Días entre registros esperados, mensual por default
            $table->decimal('target_value', 10, 2)->nullable(); // Meta numérica
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete(); // Rol responsable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};
