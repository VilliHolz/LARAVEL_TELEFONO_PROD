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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('model', 100)->default('0');
            $table->string('imei', 100)->nullable();
            $table->date('entry_date');
            $table->date('promised_date');
            $table->text('observations')->nullable();
            $table->decimal('advance', 10, 2)->default(0.00);
            $table->string('key', 200)->nullable();
            $table->string('pin', 100)->nullable();
            $table->decimal('total', 10, 2)->default(0.00);
            $table->enum('status', ['Pendiente', 'Reparado', 'Entregado', 'Cancelado'])->default('Pendiente');
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('cash_register_id');

            $table->foreign('contact_id')
                ->references('id')->on('contacts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('brand_id')
                ->references('id')->on('brands')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('branch_id')
                ->references('id')->on('branches')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('cash_register_id')
                ->references('id')->on('cash_registers')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
