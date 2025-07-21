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
        Schema::create('supplier_purchese_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('paid_status',51)->nullable(); 
            $table->double('paid_amount',15,2)->nullable();
            $table->double('due_amount',15,2)->nullable();
            $table->double('total_amount',15,2)->nullable();
            $table->double('discount_amount',15,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_purchese_payments');
    }
};
