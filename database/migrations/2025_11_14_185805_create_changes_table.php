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
        Schema::create('changes', function (Blueprint $table) {
            $table->id();

            // Relación con obra/proyecto-operativo
            $table->foreignId('worksite_id')->constrained('worksites')->cascadeOnDelete();

            $table->date('change_date');

            // Tipo de cambio (TAG)
            $table->foreignId('change_type_id')->nullable()->constrained('tags')->nullOnDelete();

            // Solicitado por (texto libre)
            $table->string('requested_by')->nullable();

            // Descripción detallada del cambio
            $table->text('description')->nullable();

            // Impacto en presupuesto (TAG opcional)
            $table->foreignId('budget_impact_id')->nullable()->constrained('tags')->nullOnDelete();

            // Estado del cambio (TAG)
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();

            // Aprobado / autorizado por (usuario)
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Observaciones internas
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('changes');
    }
};
