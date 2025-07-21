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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('type',190);
            $table->text('value')->nullable();
            $table->timestamps();
        });
        \DB::table("settings")->insert([
            [
                'type'=>"custom_sms",
                'value'=>'{"status":"0","token":"your_api_token","url":"https://login.esms.com.bd/api/v3/sms/send/","sender_id":"8809600000000","type":"plain"}',
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'type'=>"mail_config",
                'value'=>'{"status":0,"name":"demo","host":"mail.demo.com","driver":"SMTP","port":"587","username":"info@demo.com","email_id":"info@demo.com","encryption":"TLS","password":"demo"}',
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
