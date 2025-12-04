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
        Schema::create('punch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worksite_id')->constrained('worksites')->cascadeOnDelete(); // Obra asociada

            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();
            
            $table->text('observations')->nullable();
            
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punch_items');
    }
};
