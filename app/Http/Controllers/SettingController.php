<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Traits\MailAndSmsHelper;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use MailAndSmsHelper;
    public function index() {
        $sms_config = Setting::whereType('custom_sms')->first();
        return view('backend.settings.index',[
            'sms_config'=>json_decode(Setting::whereType('custom_sms')->first()?->value),
            'mail_config'=>json_decode(Setting::whereType('mail_config')->first()?->value),
        ]);
    }
    public function update_sms(Request $request){
        $this->validate($request,[
            'api_url' => 'required|url',
            'api_token' => 'required|string',
            'sender_id' => 'required|integer',
        ]);
        $sms= Setting::whereType('custom_sms')->first();
        $sms_config = json_decode($sms->value);
        $sms_config->status = $request->status??0;
        $sms_config->url = $request->api_url??'your_api_url';
        $sms_config->token = $request->api_token??'your_api_token';
        $sms_config->sender_id = $request->sender_id??'your_sender_id';
        // dd(json_encode($sms_config));
        $sms->value = json_encode($sms_config);
        if($sms->save()){
            $notification = array(
                'message' => 'Update success',
                'alert-type' => 'seccess'
            );
            return redirect()->back()->with($notification);
        }
        $notification = array(
            'message' => 'Server error',
            'alert-type' => 'error'
        );
        return redirect()->back()->with($notification);
    }

    public function sms_test(Request $request){
        $this->validate($request,[
            'number'=> 'required|string|min:11|max:11'
        ]);

        if($this->send_sms($request->number,"Dear User,\nYou have successfully configured your SMS service.")){
            $notification = array(
                'message' => 'SMS send successfully.',
                'alert-type' => 'seccess'
            );
            return redirect()->back()->with($notification);
        }
        $notification = array(
            'message' => 'SMS not send.',
            'alert-type' => 'error'
        );
        return redirect()->back()->with($notification);
    }
}
