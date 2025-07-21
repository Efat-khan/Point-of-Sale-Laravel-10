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
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->string('discount_type')->nullable()->after('unit_price');
            $table->double('discount_rate',15,2)->nullable()->after('discount_type');
            $table->double('discount_amount',15,2)->nullable()->after('discount_rate');
            $table->string('tax_type')->nullable()->after('discount_amount');
            $table->double('tax_rate',15,2)->nullable()->after('tax_type');
            $table->double('tax_amount',15,2)->nullable()->after('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_details', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_rate');
            $table->dropColumn('discount_amount');
            $table->dropColumn('tax_type');
            $table->dropColumn('tax_rate');
            $table->dropColumn('tax_amount');
        });
    }
};
