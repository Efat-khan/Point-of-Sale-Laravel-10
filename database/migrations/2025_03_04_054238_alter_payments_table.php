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
        Schema::table('payments', function (Blueprint $table) {
            $table->double('total_tax',15,2)->default(0)->after('discount_amount');
            $table->double('total_tax_rate',15,2)->default(0)->after('total_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function(Blueprint $table){
            $table->dropColumn('total_tax');
            $table->dropColumn('total_tax_rate');
        });
    }
};
