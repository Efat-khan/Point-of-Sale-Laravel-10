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
        Schema::create('supplier_purchese_details', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('buying_qty');
            $table->double('product_buying_price',15,2);
            $table->double('db_com',15,2)->default('0');
            $table->double('mc_com',15,2)->default('0');
            $table->double('sp_com',15,2)->default('0');
            $table->double('total_db_com',15,2)->default('0');
            $table->double('total_mc_com',15,2)->default('0');
            $table->double('total_sp_com',15,2)->default('0');
            $table->double('buying_price',15,2);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->tinyInteger('status')->default('1')->comment('0=Pending, 1=Approved');
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_purchese_details');
    }
};
