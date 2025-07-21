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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('alt_mobile_no')->nullable()->after('mobile_no');
            $table->string('alt_email')->nullable()->after('email');
            $table->string('office_address')->nullable()->after('address');
            $table->string('factory_address')->nullable()->after('office_address');
            $table->string('contact_person_name')->nullable()->after('factory_address');
            $table->string('contact_person_phone')->nullable()->after('contact_person_name');
            $table->string('supplier_image')->nullable()->after('contact_person_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('alt_mobile_no');
            $table->dropColumn('alt_email');
            $table->dropColumn('office_address');
            $table->dropColumn('factory_address');
            $table->dropColumn('contact_person_name');
            $table->dropColumn('contact_person_phone');
            $table->dropColumn('supplier_image');
        });
    }
};
