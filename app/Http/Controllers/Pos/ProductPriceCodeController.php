<?php

namespace App\Http\Controllers\pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceCodeRequest;
use App\Models\ProductPriceCodes;
use Illuminate\Http\Request;
use Illuminate\support\Carbon;
use Illuminate\Support\Facades\Validator;

class ProductPriceCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:product-price-code-list|product-price-code-create|product-price-code-edit|product-price-code-delete'], ['only' => ['ProductPriceCodeAll']]);
        $this->middleware(['permission:product-price-code-create'], ['only' => ['ProductPriceCodeCreate', 'ProductPriceCodeStore']]);
        $this->middleware(['permission:product-price-code-edit'], ['only' => ['ProductPriceCodeEdit', 'ProductPriceCodeUpdate']]);
        $this->middleware(['permission:product-price-code-delete'], ['only' => ['ProductPriceCodeDelete']]);
    }
    public function ProductPriceCodeAll(){
        $data = ProductPriceCodes::all();
        // dd($data);
        return view('backend.product.price_code.index',compact('data'));
    }
     
    public function ProductPriceCodeCreate(){
        return view('backend.product.price_code.create');
    }
    public function ProductPriceCodeStore(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(),[
            'number' => 'required|string|unique:product_price_codes,code',
            'code' => 'required|string|unique:product_price_codes,code',
        ]);
        // $validated = $request->validated();
        // dd($validated);

        if($validator->passes()){

            $product_price_code = new ProductPriceCodes();
            $product_price_code->number = $request->number;
            $product_price_code->code = $request->code;
            $product_price_code->save();
            $notification = array(
                'message' => 'Price Code Created Successfully',
                'alert-type' => 'success'
            );
            // Redirect or respond with success message
        }else{
            $notification = array(
                'message' => 'Duplicate Price Code Not Created.',
                'alert-type' => 'error'
            );
            }
        return redirect()->back()->with($notification);
    }

    public function ProductPriceCodeEdit($id)
    {
        $price_code = ProductPriceCodes::findOrFail($id);
        return view('backend.product.price_code.edit', compact('price_code'));
    }

    public function ProductPriceCodeUpdate(Request $request){


        ProductPriceCodes::findOrFail($request->id)->update([
                'number' => $request->number,
                'code' => $request->code,
                'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Price Code Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('productpricecode.all')->with($notification);
        } 

        public function ProductPriceCodeDelete($id)
        {
            $productPriceCode = ProductPriceCodes::findOrFail($id);
            if(!empty($productPriceCode)){
                ProductPriceCodes::findOrFail($id)->delete();
    
                $notification = array(
                    'message' => 'Price Code Deleted Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->back()->with($notification);
            }
            else{
                $notification = array(
                    'message' => 'Price Code Not Deleted Successfully',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
        } 
}
