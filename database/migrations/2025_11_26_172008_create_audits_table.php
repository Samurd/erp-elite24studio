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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();

            $table->date('date_register'); // Fecha de registro
            $table->date('date_audit'); // Fecha de auditoria
            $table->bigInteger('objective'); // Objetivo
            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete();

            $table->string('place')->nullable(); // Lugar

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();

            $table->text('observations')->nullable(); // Observaciones


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
