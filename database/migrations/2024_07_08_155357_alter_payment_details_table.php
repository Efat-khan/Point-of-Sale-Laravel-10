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
        Schema::table('payment_details', function(Blueprint $table){
            $table->string('bank_name')->nullable()->after('current_paid_amount');
            $table->string('account_no')->nullable()->after('current_paid_amount');
            $table->string('description')->nullable()->after('current_paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_details', function(Blueprint $table){
            $table->dropColumn('bank_name');
            $table->dropColumn('account_no');
            $table->dropColumn('description');
        });
    }
};
