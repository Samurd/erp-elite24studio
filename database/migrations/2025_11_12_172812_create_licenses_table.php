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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('license_type_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->string('entity')->nullable(); // Entidad tramitadora

            $table->string('company')->nullable(); // Empresa gestora 
            $table->string('eradicated_number')->nullable(); // Número de erradicado

            $table->date('eradicatd_date')->nullable();
            $table->date('estimated_approval_date')->nullable();
            $table->date('expiration_date')->nullable();

            $table->boolean('requires_extension')->default(false); // ¿Necesita prorroga?
            $table->text('observations')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
