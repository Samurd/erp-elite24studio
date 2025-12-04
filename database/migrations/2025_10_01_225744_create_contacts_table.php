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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('name', 150); // Nombre contacto
            $table->string('company', 150)->nullable();
            $table->string('email', 150)->nullable()->unique();
            $table->string('phone', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            // $table->string('associated_project', 255)->nullable();
            $table->date('first_contact_date')->nullable();
            $table->text('notes')->nullable();

            // Tags dinámicos
            $table->foreignId('contact_type_id')->nullable()->constrained('tags');
            $table->foreignId('status_id')->nullable()->constrained('tags');       // Estado: Activo, Inactivo, En evaluación
            $table->foreignId('relation_type_id')->nullable()->constrained('tags'); // Tipo relación: Cliente, Proveedor, etc.
            $table->foreignId('source_id')->nullable()->constrained('tags');        // Fuente: Web, Redes sociales, etc.
            $table->foreignId('label_id')->nullable()->constrained('tags');         // Etiqueta: LEAD NUEVO, INTERESADO, etc.

            // Relación con usuario responsable (de tabla users)
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
