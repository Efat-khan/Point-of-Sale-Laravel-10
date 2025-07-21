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
        Schema::table('purchas_details', function (Blueprint $table) {
            $table->double('db_com',15,2)->default('0')->before('total_db_com');
            $table->double('market_com',15,2)->default('0')->before('total_mc_com');
            $table->double('special_com',15,2)->default('0')->before('total_sp_com');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchas_details', function (Blueprint $table) {
            //
        });
    }
};
