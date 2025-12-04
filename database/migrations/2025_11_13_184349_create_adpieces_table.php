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
        Schema::create('adpieces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete();      // Tipo de pieza (Imagen, Video, Flyer, etc.)
            $table->foreignId('format_id')->nullable()->constrained('tags')->nullOnDelete();    // Formato (JPG, MP4, PDF, etc.)
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();    // Estado (Diseño inicial, En revisión, Aprobada)
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete(); // Proyecto vinculado
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();     // Responsable
            $table->foreignId('strategy_id')->nullable()->constrained('strategies')->nullOnDelete(); // Campaña / estrategia vinculada

            // Campos base
            $table->string('name');                         // Nombre de la pieza
            $table->string('media')->nullable();            // Medio o canal (Instagram, TikTok, etc.)
            $table->text('instructions')->nullable();       // Comentarios / instrucciones
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adpieces');
    }
};
