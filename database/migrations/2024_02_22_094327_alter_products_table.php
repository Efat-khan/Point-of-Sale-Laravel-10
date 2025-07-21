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
        Schema::table('products',function(Blueprint $table){
            $table->string('sku')->nullable()->after('name');
            $table->double('product_buying_price',15,2)->after('product_sort_name');
            $table->double('product_offer_price',15,2)->after('product_price')->nullable();
            $table->string('product_price_code')->after('product_offer_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table( 'products', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('product_buying_price');
            $table->dropColumn('product_offer_price');
            $table->dropColumn('product_price_code');
        });
    }
};
