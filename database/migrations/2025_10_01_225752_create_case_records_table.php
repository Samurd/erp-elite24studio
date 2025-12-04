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
        Schema::create('case_records', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->text('channel')->nullable();

            // referencias a tags (category = case_status, case_type, case_priority)
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete();
            $table->foreignId('case_type_id')->nullable()->constrained('tags')->nullOnDelete();

            // asignado a usuario (jetstream users)
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_records');
    }
};
