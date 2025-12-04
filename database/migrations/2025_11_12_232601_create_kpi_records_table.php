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
        Schema::create('kpi_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpis')->cascadeOnDelete();
            $table->date('record_date')->nullable(); // Fecha del registro del indicador
            $table->decimal('value', 10, 2)->nullable(); // Valor numérico del indicador
            $table->text('observation')->nullable(); // Comentarios u observaciones
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete(); // Usuario que registró
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_records');
    }
};
