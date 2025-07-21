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
        Schema::table('invoices',function(Blueprint $table){
            $table->string('invoice_tax_type')->nullable()->after('invoice_no');
            $table->decimal('invoice_tax_rate')->nullable()->after('invoice_tax_type');
            $table->decimal('invoice_tax_amount')->nullable()->after('invoice_tax_rate');
            $table->string('invoice_discount_type')->nullable()->after('invoice_tax_rate');
            $table->decimal('invoice_discount_amount')->nullable()->after('invoice_discount_type');
            $table->string('additional_charge_type')->nullable()->after('invoice_discount_type');
            $table->decimal('additional_charge_amount')->nullable()->after('additional_charge_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices',function(Blueprint $table){
            $table->dropColumn('invoice_tax_type');
            $table->dropColumn('invoice_tax_rate');
            $table->dropColumn('invoice_tax_amount');
            $table->dropColumn('invoice_discount_type');
            $table->dropColumn('invoice_discount_amount');
            $table->dropColumn('additional_charge_type');
            $table->dropColumn('additional_charge_amount');

        });
    }
};
