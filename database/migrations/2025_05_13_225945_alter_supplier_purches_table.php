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
            $table->string('po_no')->nullable()->after('purchase_no');
            $table->string('dn_no')->nullable()->after('po_no');
            $table->string('wo_no')->nullable()->after('dn_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('supplier_purcheses',function(Blueprint $table){
            $table->dropColumn('po_no');
            $table->dropColumn('dn_no');
            $table->dropColumn('wo_no');
        });
    }
};
