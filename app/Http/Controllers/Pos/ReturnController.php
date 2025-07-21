<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ReturnDetail;
use Auth;
use Illuminate\support\Carbon;

class ReturnController extends Controller
{

     // Return details form for show data
     public function ReturnAll(){
        $allData = ReturnDetail::orderBy('date','desc')->orderBy('id','desc')->get();
        return view('backend.return.return_all',compact('allData'));

    }// End Method


     // Return insert form
     public function ReturnAdd(){
        $product = Product::all();
        $supplier = Supplier::all();
        $category = Category::all();
        $date = date('Y-m-d');
        return view('backend.return.return_add',compact('product','supplier','category','date'));
    }//End Method


  // Return item save to Database
  public function ReturnStore(Request $request ){

    if($request->category_id== null){
        $notification = array(
            'message' => 'Sorry you do not select any Item.',
            'alert-type' => 'error'
        );
        return redirect()->back()->with($notification);
    } 
    else{
        $count_category = count($request->category_id);
        for($i = 0; $i < $count_category; $i++){
            $return = new ReturnDetail();
            $return->date = date('Y-m-d', strtotime($request->date[$i]));
            $return->invoice_no = $request->invoice_no[$i]; 
            $return->supplier_id = $request->supplier_id[$i]; 
            $return->category_id = $request->category_id[$i]; 
            $return->product_id = $request->product_id[$i]; 
            $return->return_qty = $request->return_qty[$i]; 
            $return->unit_price = $request->unit_price[$i]; 
            $return->description = $request->description[$i]; 
            $return->return_price = $request->return_price[$i]; 

            $return->status = '0';

            $return->created_by = Auth::user()->id; 
            $return->created_at = Carbon::now(); 

            $return->save();

        } // end of else
    } // end of For loop

    $notification = array(
        'message' => 'Data Saved Successfully',
        'alert-type' => 'success'
    );
    return redirect()->back()->with($notification);
    // return redirect()->route('return.all')->with($notification);
}//End Method



    // Return Delete from database
    public function ReturnDelete($id){
        $return = ReturnDetail::findOrFail($id);
        
        if($return->status == '0'){
            ReturnDetail::findOrFail($id)->delete();
        }
        
        $notification = array(
            'message' => 'Return Pending Item Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }//End Method

       // Return pending page
       public function ReturnPending(){
        $allData = ReturnDetail::orderBy('date','desc')->orderBy('id','desc')->where('status','0')->get();
        return view('backend.return.return_pending',compact('allData'));
    }//End Method

    
    // Return Approval
    public function ReturnApprove($id){
        $return = ReturnDetail::findOrFail($id);
        $product = Product::where('id', $return->product_id)->first();
        $return_qty = ((float)( $return->return_qty)) + ((float)($product->quantity));
        $product->quantity = $return_qty;

        if($product->save()){
            ReturnDetail::findOrFail($id)->update([
                'status' => '1',
            ]);

            $notification = array(
                'message' => 'Status Approved Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('return.pending')->with($notification);
        }

    }//End Method

     // Daily Return Report
     public function DailyReturnReport(){
        return view('backend.return.daily_return_report');
        
    }//End Method


     // Daily Return PDF report generating
     public function DailyReturnPdf(Request $request){
        $start_date = date('Y-m-d', strtotime($request->start_date));
        $end_date = date('Y-m-d', strtotime($request->end_date));
        $allData = ReturnDetail::whereBetween('date',[$start_date,$end_date])->where('status','1')->get();

        return view('backend.pdf.daily_return_report_pdf',compact('allData','start_date','end_date'));
       
    }// End Method



}
