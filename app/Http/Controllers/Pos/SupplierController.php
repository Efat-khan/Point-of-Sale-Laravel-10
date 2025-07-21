<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Auth;
use Image;
use Illuminate\support\Carbon;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:supplier-list|supplier-create|supplier-edit|supplier-delete'], ['only' => ['SupplierAll']]);
        $this->middleware(['permission:supplier-create'], ['only' => ['SupplierAdd', 'SupplierStore']]);
        $this->middleware(['permission:supplier-edit'], ['only' => ['SupplierEdit', 'SupplierUpdate']]);
        $this->middleware(['permission:supplier-delete'], ['only' => ['SupplierDelete']]);
    }
    // Supplier show from database
    public function SupplierAll(Request $request){
        $suppliers_for_filter = Supplier::latest()->get();
        $supplier_id = $request->get('supplier_filter');
        // dd($supplier_id);
        if ($supplier_id) {
            $suppliers = Supplier::where('id', $supplier_id)->latest()->get();
        } else {
            $suppliers = Supplier::latest()->get();
        }
        return view('backend.supplier.supplier_all', compact('suppliers','supplier_id','suppliers_for_filter'));
    }//End Method


    // Supplier insert form
    public function SupplierAdd(){
        return view('backend.supplier.supplier_add');
    }//End Method


    // Save supplier insert form to Database
    public function SupplierStore(Request $request){
        if ($request->file('supplier_image')) {
            $image = $request->file('supplier_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension(); // Ex. 34343.jpg 
            Image::make($image)->resize(200, 200)->save('upload/supplier_images/' . $name_gen);
            $save_url = 'upload/supplier_images/' . $name_gen;

            Supplier::insert([
                'name' => $request->name,
                'mobile_no' => $request->mobile_no,
                'alt_mobile_no' => $request->alt_mobile_no,
                'email' => $request->email,
                'alt_email' => $request->alt_email,
                'office_address' => $request->office_address,
                'factory_address' => $request->factory_address,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'supplier_image' => $save_url,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        } else {
            Supplier::insert([
                'name' => $request->name,
                'mobile_no' => $request->mobile_no,
                'alt_mobile_no' => $request->alt_mobile_no,
                'email' => $request->email,
                'alt_email' => $request->alt_email,
                'office_address' => $request->office_address,
                'factory_address' => $request->factory_address,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'supplier_image' => 'upload/no_image.jpg',
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }
        $notification = array(
            'message' => 'Supplier Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('supplier.all')->with($notification);
    }//End Method

    // Supplier Edit form
    public function SupplierEdit($id){
        $supplier = Supplier::findOrFail($id);
        return view('backend.supplier.supplier_edit',compact('supplier'));
    }//End Method

    // Supplier edited data save to database
    public function SupplierUpdate(Request $request){
        $supplier_id = $request->id;

        if ($request->file('supplier_image')) {
            $image = $request->file('supplier_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension(); // Ex. 34343.jpg 
            Image::make($image)->resize(200, 200)->save('upload/supplier_images/' . $name_gen);
            $save_url = 'upload/supplier_images/' . $name_gen;

            Supplier::findOrFail($supplier_id)->update([
                'name' => $request->name,
                'supplier_image' => $save_url,
                'mobile_no' => $request->mobile_no,
                'alt_mobile_no' => $request->alt_mobile_no,
                'email' => $request->email,
                'alt_email' => $request->alt_email,
                'office_address' => $request->office_address,
                'factory_address' => $request->factory_address,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Supplier Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('supplier.all')->with($notification);
        } else {
            Supplier::findOrFail($supplier_id)->update([
                'name' => $request->name,
                'mobile_no' => $request->mobile_no,
                'alt_mobile_no' => $request->alt_mobile_no,
                'email' => $request->email,
                'alt_email' => $request->alt_email,
                'office_address' => $request->office_address,
                'factory_address' => $request->factory_address,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Supplier Updated Successfully without image',
                'alert-type' => 'success'
            );
            return redirect()->route('supplier.all')->with($notification);
        } // End of Else
    } //End Method

    // Supplier Details Pdf
    public function SupplierAllReportPdf($id = null)
    {
        if ($id != null) {
            $suppliers = Supplier::where('id', $id)->latest()->get();
        } else {
            $suppliers = Supplier::latest()->get();
        }
        return view('backend.supplier.pdf.supplier_all_report', compact('suppliers'));
    }
    // Supplier Delete from database
    public function SupplierDelete($id){
        Supplier::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Supplier Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }//End Method
}
