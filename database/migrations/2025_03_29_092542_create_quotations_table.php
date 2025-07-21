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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quotation_no')->unique(); // Assuming it's a unique number
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Assuming it's a unique number
            $table->string('dn_no', 50)->unique(); // Delivery Note Number
            $table->string('wo_no', 50)->unique(); // Work Order Number
            $table->string('title', 150)->nullable();
            $table->string('company_name', 150);
            $table->string('company_address', 255)->nullable();
            $table->string('company_phone', 20)->nullable();
            $table->string('company_email', 100)->nullable();
            $table->date('date');
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->string('currency', 3)->default('USD'); // Standard currency code length is 3 (ISO 4217)
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
