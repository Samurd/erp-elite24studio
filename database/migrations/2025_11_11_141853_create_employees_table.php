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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Work Information
            $table->string('full_name');
            $table->string('job_title');
            $table->string('work_email')->unique();
            $table->string('mobile_phone');
            $table->string('curriculum_file')->nullable();
            $table->text('work_address');
            $table->string('work_schedule')->default('40 hours/week');

            // Private Information
            $table->text('home_address')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('private_phone')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_certificate_file')->nullable();
            $table->string('identification_number')->unique();
            $table->string('social_security_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('birth_country')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->text('disability_details')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');

            // Education
            $table->unsignedBigInteger('education_type_id')->nullable();

            // Family Status
            $table->unsignedBigInteger('marital_status_id')->nullable();
            $table->integer('number_of_dependents')->default(0);

            $table->timestamps();

            // Foreign keys

            // Department
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreign('gender_id')->references('id')->on('tags')->onDelete('set null');
            $table->foreign('education_type_id')->references('id')->on('tags')->onDelete('set null');
            $table->foreign('marital_status_id')->references('id')->on('tags')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
