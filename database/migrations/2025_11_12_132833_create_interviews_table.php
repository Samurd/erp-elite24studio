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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();

            // fecha y hora
            $table->date('date');
            $table->time('time')->nullable();

            // entrevistador
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->nullOnDelete();

            // tipo, estado y resultado vienen de tags
            $table->foreignId('interview_type_id')->nullable()->constrained('tags')->nullOnDelete(); // ejemplo: Técnica, Filtro, Final
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // Programada, Realizada, Cancelada, Reprogramada
            $table->foreignId('result_id')->nullable()->constrained('tags')->nullOnDelete(); // Apto, No Apto, Requiere Prueba Adicional

            $table->string('platform')->nullable();               // e.g. "Google Meet"
            $table->string('platform_url')->nullable();           // e.g. meet.google.com/...
            $table->text('expected_results')->nullable();         // "Habilidad en SketchUp, claridad conceptual"
            $table->text('interviewer_observations')->nullable(); // observaciones del entrevistador
            $table->decimal('rating', 3, 1)->nullable();          // 8.5 / 10
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
