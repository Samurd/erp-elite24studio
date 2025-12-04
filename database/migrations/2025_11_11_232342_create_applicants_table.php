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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
              $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('tags')->nullOnDelete(); // tag: candidate_status
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
