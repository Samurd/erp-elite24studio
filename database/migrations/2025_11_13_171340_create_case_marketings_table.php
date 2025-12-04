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
        Schema::create('case_marketings', function (Blueprint $table) {
            $table->id();
            $table->string('subject'); // Nombre o asunto del caso
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date')->nullable();
            $table->string('mediums')->nullable(); // Ej: Web, RRSS, Chatbot
            $table->text('description')->nullable(); // Description or details
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete();

            // Relaciones con tags
            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete(); // Tipo de caso
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_marketings');
    }
};
