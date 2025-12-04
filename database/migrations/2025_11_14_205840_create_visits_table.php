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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('worksite_id')->constrained('worksites')->cascadeOnDelete();

            // Fecha de visita
            $table->date('visit_date')->nullable();

            // Usuario que realizó la visita
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();

            // Observaciones generales
            $table->text('general_observations')->nullable();

            // Estado de la visita (TAG)
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();

            // Notas internas
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
