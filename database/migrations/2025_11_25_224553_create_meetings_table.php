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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Titulo de la reunion


            $table->date('date'); // Fecha de la reunion

            $table->time('start_time'); // Hora de inicio
            $table->time('end_time'); // Hora de fin

            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete(); // Equipo encargado

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // Estado de la reunion

            
            $table->text('notes')->nullable(); // Notas previas

            $table->string('url')->nullable(); // URL de la reunión (Zoom, Teams, etc.)
            
            
            // Campos post reunion

            $table->text('observations')->nullable(); // Observaciones finales, asistencias confirmaron presencia, evaluacion general, etc.


            $table->boolean('goal')->default(false); // Meta cumplida?

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
