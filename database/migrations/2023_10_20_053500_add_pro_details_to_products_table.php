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
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_sort_name')->nullable()->after('name');
            $table->string('product_code')->nullable()->after('name');
            $table->double('product_price',15,2)->default('0')->after('quantity');//is selling price
            $table->double('db_com',15,2)->default('0')->after('quantity');
            $table->double('market_com',15,2)->nullable()->after('quantity');
            $table->double('special_com',15,2)->nullable()->after('quantity');
            $table->string('barcode_type')->nullable()->after('quantity');
            $table->string('description')->nullable()->after('quantity');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
