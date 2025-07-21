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
        Schema::table('products', function(Blueprint $table){
            $table->enum('tax_type',['Included','TaxFree'])->default('TaxFree')->after('product_price_code');
            $table->double('tax',15,2)->default(0)->after('tax_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function(Blueprint $table){
            $table->dropColumn('tax_type');
            $table->dropColumn('tax');
        });
    }
};
