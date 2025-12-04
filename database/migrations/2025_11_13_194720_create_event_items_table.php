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
        Schema::create('event_items', function (Blueprint $table) {
            $table->id();
            // Relación con el evento padre
            $table->foreignId('event_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('description'); // Columna "Item"
            $table->integer('quantity');   // Columna "Cantidad"
            $table->foreignId('unit_id')->nullable()->constrained('tags')->nullOnDelete();        // Columna "Unidad" (und, pauta, pieza)

            // Dinero
            $table->bigInteger('unit_price'); // Valor Unitario
            $table->bigInteger('total_price'); // Total (cant * unitario)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_items');
    }
};
