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
        Schema::create('payments_sales', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->timestamps();
            $table->unsignedBigInteger('credit_id');
            $table->unsignedBigInteger('cash_register_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('cash_register_id')
                ->references('id')->on('cash_registers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('credit_id')
                ->references('id')->on('credit_sales')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_sales');
    }
};
