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
        Schema::create('apu_campaigns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->text('description');
            $table->bigInteger('quantity');
            $table->foreignId('unit_id')->nullable()->constrained('tags')->nullOnDelete();

            $table->bigInteger('unit_price')->nullable(); // Valor Unitario
            $table->bigInteger('total_price')->nullable(); // Total (cant * unitario)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apu_campaigns');
    }
};
