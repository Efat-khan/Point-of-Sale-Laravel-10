<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:brand-list|brand-create|brand-edit|brand-delete'], ['only' => ['brandAdd']]);
        $this->middleware(['permission:brand-create'], ['only' => ['brandStore']]);
        $this->middleware(['permission:brand-edit'], ['only' => ['brandEdit', 'brandUpdate']]);
        $this->middleware(['permission:brand-delete'], ['only' => ['brandDelete']]);
    }
    // Brand insert form
    public function brandAdd()
    {
        $brands = Brand::latest()->get();
        return view('backend.brand.brand_add',compact('brands'));
    } //End Method

    // Save unit insert form to Database
    public function brandStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|unique:brands,name'
        ]);

        if($validator->passes()){

            Brand::insert([
                'name' => $request->name,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
    
            $notification = array(
                'message' => 'Brand Inserted Successfully',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'message' => 'Warning! Try again.',
                'alert-type' => 'warning'
            );
        }
        return redirect()->route('brand.add')->with($notification);
    } //End Method

    // Brand Edit form
    public function brandEdit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('backend.brand.brand_edit', compact('brand'));
    } //End Method

    // Brand edited data save to database
    public function brandUpdate(Request $request)
    {
        $brand_id = $request->id;
        Brand::findOrFail($brand_id)->update([
            'name' => $request->name,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Brand Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('brand.add')->with($notification);
    } //End Method

    // Brand Delete from database
    public function brandDelete($id){

        Brand::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Brand Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }//End Method

}
