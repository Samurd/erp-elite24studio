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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->bigInteger('subtotal'); // Subtotal - servicio/pago
            $table->bigInteger('bonos')->nullable();
            $table->bigInteger('deductions')->nullable(); // Deducciones

            $table->bigInteger('total'); // Total

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();

            $table->text('observations')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
