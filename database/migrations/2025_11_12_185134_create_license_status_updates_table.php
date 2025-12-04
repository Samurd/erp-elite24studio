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
        Schema::create('license_status_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained('licenses')->onDelete('cascade');

            $table->date('date')->nullable();
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->text('description')->nullable(); // Descripción o comentario del avance
            $table->text('internal_notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_status_updates');
    }
};
