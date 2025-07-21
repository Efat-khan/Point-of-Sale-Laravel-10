<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Customer;
use App\Models\Unit;
use App\Models\DeliveryZone;
use Auth;
use Illuminate\support\Carbon;
use DB;

class DeliveryzoneController extends Controller
{
     // Category all show
     public function DeliveryzoneAll(){
        $deliveryzone = DeliveryZone::latest()->get();
        return view('backend.deliveryzone.deliveryzone_all',compact('deliveryzone'));
    }// End Method

     // DeliveryzoneAdd insert form
     public function DeliveryzoneAdd(){
        return view('backend.deliveryzone.deliveryzone_add');
    }//End Method

    //  DeliveryzoneStore insert form to Database
     public function DeliveryzoneStore(Request $request){
        DeliveryZone::insert([
            'delivery_zone' => $request->delivery_zone,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Delivery Zone Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('deliveryzone.all')->with($notification);

    }//End Method


    
       // DeliveryzoneEdit Edit form
       public function DeliveryzoneEdit($id){
        $deliveryzone = DeliveryZone::findOrFail($id);
        return view('backend.deliveryzone.deliveryzone_edit',compact('deliveryzone'));
    }//End Method


    
    // Category edited data save to database
    public function DeliveryzoneUpdate(Request $request){
        $deliveryzone_id = $request->id;
        DeliveryZone::findOrFail($deliveryzone_id)->update([
            'delivery_zone' => $request->delivery_zone,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Delivery Zone Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('deliveryzone.all')->with($notification);
    }//End Method


    
    // DeliveryzoneDelete Delete from database
    public function DeliveryzoneDelete($id){

        DeliveryZone::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Delivery Zone Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }//End Method

}

    
