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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();


            $table->string('name');
            $table->text('description')->nullable();

            // Relaciones con Tags
            $table->foreignId('type_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();

            // fecha de emisión opcional
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();

            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
