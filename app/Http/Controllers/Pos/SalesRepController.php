<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\SalesRep;
use Auth;
use Illuminate\support\Carbon;
use Image;

class SalesRepController extends Controller
{
       // all SR show
       public function SrAll(){
        $sr_all = SalesRep::latest()->get();
        return view('backend.sr.sr_all',compact('sr_all'));

    }//End Method


      // Add SR
    public function SrAdd(){
        return view('backend.sr.sr_add');
    }//End Method


     // SR save form
     public function SrStore(Request $request){

        if($request->file('sr_image')){
            $image = $request->file('sr_image');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension(); // Ex. 34343.jpg 
            Image::make($image)->resize(200,200)->save('upload/sr_images/'.$name_gen);
            $save_url = 'upload/sr_images/'.$name_gen;

            SalesRep::insert([
                'name' => $request->name,
                'sr_image' => $save_url,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'address' => $request->address,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }
        else{
            SalesRep::insert([
                'name' => $request->name,
                'sr_image' => 'upload/no_image.jpg',
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'address' => $request->address,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }

        $notification = array(
            'message' => 'SR Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('sr.all')->with($notification);

    }//End Method

    public function SrEdit($id){
        $sr = SalesRep::findOrFail($id);
        return view('backend.sr.sr_edit',compact('sr'));
    }//End Method


    // sr edited data save to database
    public function SrUpdate(Request $request){

        $sr_id = $request->id;

        if($request->file('sr_image')){
            $image = $request->file('sr_image');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension(); // Ex. 34343.jpg 
            Image::make($image)->resize(200,200)->save('upload/sr_images/'.$name_gen);
            $save_url = 'upload/sr_images/'.$name_gen;

            SalesRep::findOrFail($sr_id )->update([
                'name' => $request->name,
                'sr_image' => $save_url,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'address' => $request->address,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
    
            $notification = array(
                'message' => 'SR Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('sr.all')->with($notification);

        }
        else{
            SalesRep::findOrFail($sr_id )->update([
                'name' => $request->name,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'address' => $request->address,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
    
            $notification = array(
                'message' => 'SR Updated Successfully without image',
                'alert-type' => 'success'
            );
            return redirect()->route('sr.all')->with($notification);
        }// End of Else
        
    }//End Method


     // SR Delete form
     public function SrDelete($id){
        
        $sr = SalesRep::findOrFail($id);
        $img = $sr->sr_image;

        if($img != 'upload/no_image.jpg'){
            unlink($img);
        }
        SalesRep::findOrFail($id)->delete();

        $notification = array(
            'message' => 'SR Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }//End Method


}
