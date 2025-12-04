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
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la estrategia
            $table->string('objective')->nullable(); // Objetivo principal

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // Estado
            $table->date('start_date')->nullable(); // Fecha de inicio
            $table->date('end_date')->nullable();   // Fecha de finalización

            $table->string('target_audience')->nullable(); // Público objetivo
            $table->string('platforms')->nullable(); // Plataformas involucradas

            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete(); // Responsable

            $table->boolean('notify_team')->default(false); // Notificar equipo
            $table->boolean('add_to_calendar')->default(false); // Agregar al calendario

            $table->text('observations')->nullable(); // Observaciones
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strategies');
    }
};
