<?php
namespace App\Traits;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

trait MailAndSmsHelper{
    public function send_sms(string $number,$message) {
        $sms_config = json_decode(Setting::whereType('custom_sms')->first()?->value);
        $number = str_replace(array('+88', '88'), array('', ''), $number);
        if($sms_config?->status !=1 || $this->mobileNoValidator($number) == false){
            return false;
        }
        
        $response = Http::withHeaders([
            'Authorization' => "Authorization: Bearer $sms_config?->token",
            'Accept' => 'application/json'
        ])->post($sms_config?->url, [
            'recipient' => "88$number",
            'sender_id' => $sms_config?->sender_id,
            'type' => 'plain',
            'message' => $message,
        ]);
        if($response && json_decode($response)?->status == 'success'){
            return true;
        }
        return false;
    }

    public function mobileNoValidator(string $Number) {
        $ValidCode = ['013', '014', '015', '016', '017', '018', '019'];


        if (strlen($Number) > 11 || strlen($Number) < 11) {
            
            return false;
        }

        if (!in_array(substr($Number, 0, 3), $ValidCode)) {
            return false;
        }

        return true;
    }
}