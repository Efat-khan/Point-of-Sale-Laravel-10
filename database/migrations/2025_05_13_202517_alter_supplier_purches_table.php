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
        Schema::table('supplier_purcheses',function(Blueprint $table){
            $table->enum('purchase_type',['purchase','draft'])->default('draft')->after('purchase_no');
            $table->string('purchase_tax_type')->nullable()->after('purchase_type');
            $table->decimal('purchase_tax_rate',12,2)->nullable()->after('purchase_tax_type');
            $table->decimal('purchase_tax_amount',12,2)->nullable()->after('purchase_tax_rate');
            $table->string('purchase_discount_type')->nullable()->after('purchase_tax_amount');
            $table->double('purchase_discount_rate',12,2)->nullable()->after('purchase_discount_type');
            $table->decimal('purchase_discount_amount',12,2)->nullable()->after('purchase_discount_rate');
            $table->string('additional_charge_type')->nullable()->after('purchase_discount_amount');
            $table->decimal('additional_charge_amount',12,2)->nullable()->after('additional_charge_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_purcheses',function(Blueprint $table){
            $table->dropColumn('purchase_tax_type');
            $table->dropColumn('purchase_tax_rate');
            $table->dropColumn('purchase_tax_amount');
            $table->dropColumn('purchase_discount_type');
            $table->dropColumn('purchase_discount_rate');
            $table->dropColumn('purchase_discount_amount');
            $table->dropColumn('additional_charge_type');
            $table->dropColumn('additional_charge_amount');
        });
    }
};
