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
        Schema::create("expenses", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table
                ->foreignId("category_id")
                ->nullable()
                ->constrained("tags")
                ->nullOnDelete();
            $table->text("description")->nullable();
            $table->bigInteger("amount")->nullable();
            $table
                ->foreignId("result_id")
                ->nullable()
                ->constrained("tags")
                ->nullOnDelete();
            $table->date("date")->nullable();
            $table
                ->foreignId("created_by_id")
                ->nullable()
                ->constrained("users")
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("expenses");
    }
};
