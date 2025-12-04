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
        Schema::create('tax_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->string('entity');
            $table->bigInteger('base'); // Base gravable
            $table->bigInteger('porcentage');
            $table->bigInteger('amount'); // valor retenido/pagado
            $table->date('date'); // Fecha del pago
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_records');
    }
};
