<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductLabelsPrintController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:product-label-list'], ['only' => ['index']]);
    }
    public function index(Request $request){
        // $products = Product::all();
        // dd($products);
        return view('backend.product.print_labels.index');
    }
    public function labelPrint(Request $request){
        $qty = $request->qty;
        $product = Product::findOrFail($request->product_id);
        
        return view('backend.pdf.product_labels_print',compact('product','qty'));
    }
}
