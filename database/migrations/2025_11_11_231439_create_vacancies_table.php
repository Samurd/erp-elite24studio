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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('area')->nullable(); // Área o departamento
            $table->foreignId('contract_type_id')->nullable()->constrained('tags')->nullOnDelete(); // Tag tipo de contrato
            $table->date('published_at')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // Estado del proceso (en revisión, cerrado, etc)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Responsable (ej: Karla V.)
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
