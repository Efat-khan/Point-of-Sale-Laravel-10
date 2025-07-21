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
        Schema::table('invoice_details',function(Blueprint $table){
            $table->double('profit',15,2)->after('unit_price');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_details',function(Blueprint $table){
            $table->dropColumn('buying_price');
            $table->dropColumn('profit');

        });
    }
};
