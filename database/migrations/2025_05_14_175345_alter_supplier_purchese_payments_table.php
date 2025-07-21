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
        Schema::table('supplier_purchese_payments', function (Blueprint $table) {
            $table->double('total_tax_amount', 15, 2)->nullable()->after('discount_amount');
            $table->double('total_additional_charge_amount', 15, 2)->nullable()->after('total_tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('total_tax_amount');
            $table->dropColumn('total_additional_charge_amount');
        });
    }
};
