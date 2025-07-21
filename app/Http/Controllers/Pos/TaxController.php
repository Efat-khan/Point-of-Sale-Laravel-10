<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:tax-list|tax-create|tax-edit|tax-delete'], ['only' => ['index']]);
        $this->middleware(['permission:tax-create'], ['only' => ['store']]);
        $this->middleware(['permission:tax-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:tax-delete'], ['only' => ['delete']]);
    }
    // Index
    public function index()
    {
        $data = Tax::latest()->get();
        return view('backend.tax.create',compact('data'));
    } //End Method

    // Store
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|unique:taxes,name',
            'rate' => 'required|numeric',
        ]);

        if($validator->passes()){

            Tax::insert([
                'name' => $request->name,
                'rate' => $request->rate/100,
                'created_at' => Carbon::now(),
            ]);
    
            $notification = array(
                'message' => 'Tax Create Successfully.',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'message' => 'Warning! Try again.',
                'alert-type' => 'warning'
            );
        }
        return redirect()->route('tax.index')->with($notification);
    } //End Method

    // Edit
    public function edit($id)
    {
        $data = Tax::findOrFail($id);
        return view('backend.tax.edit', compact('data'));
    } //End Method

    // Update
    public function update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'name' => 'required|string'
        ]);
        if($validator->passes()){

            Tax::findOrFail($request->id)->update([
                'name' => $request->name,
                'rate' => $request->rate/100,
                'updated_at' => Carbon::now(),
            ]);
    
            $notification = array(
                'message' => 'Tax Updated Successfully',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'message' => 'Warning! Try again.',
                'alert-type' => 'warning'
            );
        }
        

        
        return redirect()->route('tax.index')->with($notification);
    } //End Method

    // Delete
    public function delete($id){
        Tax::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Tax Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }//End Method
}
