<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Category;
use Auth;
use Illuminate\support\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:category-list|category-create|category-edit|category-delete'], ['only' => ['CategoryAdd']]);
        $this->middleware(['permission:category-create'], ['only' => ['CategoryStore']]);
        $this->middleware(['permission:category-edit'], ['only' => ['CategoryEdit', 'CategoryUpdate']]);
        $this->middleware(['permission:category-delete'], ['only' => ['categoryDelete']]);
    }
    // Category insert form
    public function CategoryAdd(){
        $brands=Brand::all();
        $categories = Category::latest()->get();
        return view('backend.category.category_add',compact('brands','categories'));
    }//End Method

     // Save unit insert form to Database
     public function CategoryStore(Request $request){
        // dd($request->all());
        if($request->brand_id != -1 ){
            $validator = Validator::make($request->all(),[
                'name' => 'required'
            ]);
            if($validator->passes()){

                Category::insert([
                    'name' => $request->name,
                    'brand_id' => $request->brand_id,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now(),
                ]);
                $notification = array(
                    'message' => 'Category Created Successfully',
                    'alert-type' => 'success'
                );
            }else{
                $notification = array(
                    'message' => 'Warning! Try again.',
                    'alert-type' => 'warning'
                );
            }
        }else{
            $validator = Validator::make($request->all(),[
                'brand_name' => 'required|string|unique:brands,name',
                'name' => 'required',
            ]);
            if($validator->passes()){

                $brand = new Brand();
                $brand->name = $request->brand_name;
                $brand->created_by = Auth::user()->id;
                $brand->created_at = Carbon::now();
                DB::transaction(function () use ($request, $brand) {
                    if ($brand->save()) {
                        Category::insert([
                            'name' => $request->name,
                            'brand_id' => $brand->id,
                            'created_by' => Auth::user()->id,
                            'created_at' => Carbon::now(),
                        ]);
                    }});
                $notification = array(
                    'message' => 'Brand & Category Created Successfully',
                    'alert-type' => 'success'
                );
            }else{
                $notification = array(
                    'message' => 'Warning! Try again.',
                    'alert-type' => 'warning'
                );
            }
        }
        
        return redirect()->route('category.add')->with($notification);

    }//End Method
    
    // Category Edit form
    public function CategoryEdit($id){
        $category = Category::findOrFail($id);
        $brands = Brand::all();
        return view('backend.category.category_edit',compact('category','brands'));
    }//End Method

    // Category edited data save to database
    public function CategoryUpdate(Request $request){
        $category_id = $request->id;
        if($request->brand_id != -1){
            $validator = Validator::make($request->all(),[
                'name' => 'required'
            ]);
            if($validator->passes()){
            Category::findOrFail($category_id)->update([
                'name' => $request->name,
                'brand_id' => $request->brand_id,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);
    
            $notification = array(
                'message' => 'Category Updated Successfully',
                'alert-type' => 'success'
            );
        }else{
                $notification = array(
                    'message' => 'Warning! Try again.',
                    'alert-type' => 'warning'
                );
            }
        }else{
            $validator = Validator::make($request->all(),[
                'brand_name' => 'required|string|unique:brands,name',
                'name' => 'required',
            ]);
            if($validator->passes()){
            $brand = new Brand();
            $brand->name = $request->brand_name;
            $brand->created_by = Auth::user()->id;
            $brand->created_at = Carbon::now();
            DB::transaction(function () use ($request, $brand) {
                $category_id = $request->id;
                if ($brand->save()) {
                    Category::findOrFail($category_id)->update([
                        'name' => $request->name,
                        'brand_id' => $brand->id,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => Carbon::now(),
                    ]);
            
                }});
                $notification = array(
                    'message' => 'Category Updated Successfully & A New Brand Created.',
                    'alert-type' => 'success'
                );
            }else{
                $notification = array(
                    'message' => 'Warning! Try again.',
                    'alert-type' => 'warning'
                );
            }
        }
        return redirect()->route('category.add')->with($notification);
    }//End Method

    // Brand Delete from database
    public function categoryDelete($id){
        Category::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Category Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }//End Method
}
