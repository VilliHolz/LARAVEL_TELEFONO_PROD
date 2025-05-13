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
            $table->enum('type', ['client', 'supplier']);
            $table->string('name', 100);
            $table->string('phone', 20);
            $table->string('address', 255);
            $table->string('email', 100);
            $table->enum('status', ['Activo', 'Inactivo'])->default('Activo');
            $table->timestamps();
            $table->unsignedBigInteger('branch_id');

            $table->foreign('branch_id')
                ->references('id')->on('branches')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
