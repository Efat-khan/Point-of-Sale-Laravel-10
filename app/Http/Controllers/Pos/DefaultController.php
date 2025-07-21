<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\ProductTax;
use App\Models\Tax;
use App\Models\Unit;
use Auth;
use Illuminate\support\Carbon;

class DefaultController extends Controller
{

    // Category load based on Brand
    public function GetCategoryByBrand(Request $request){
        $brand_id = $request->brand_id;
     
        $allCategory = Category::where('brand_id',$brand_id)->get();
        // dd($allCategory);
        return response()->json($allCategory);

    } // End Method
    // Category load based on Supplier page: purchase_add
    public function GetCategory(Request $request){
        $supplier_id = $request->supplier_id;
        // dd($supplier_id);
        $allCategory = Product::with(['category'])->select('category_id')->where('supplier_id',$supplier_id)->groupBy('category_id')->get();
        // dd($allCategory);
        return response()->json($allCategory);

    } // End Method


    // Product load based on Category page: purchase_add
    public function GetProduct(Request $request){
        
        $category_id = $request->category_id;
        // dd($category_id);
        $allProduct = Product::where('category_id',$category_id)->get();
        // dd($allCategory);
        return response()->json($allProduct);

    } // End Method
    
    // Stock Method to get the stock data
    
    public function GetStock(Request $request){
        $product_id =  $request->product_id;
        $stock = Product::where('id', $product_id)->first()->quantity;
        return response()->json($stock);
        
    }
    // Product load get-product-for-invoice
    public function GetProductForInvoice(Request $request){
        
        $product_id = $request->product_id;
        $product = Product::findOrFail($product_id);
        $product_taxes = ProductTax::where('product_id', $product->id)->get();
        $selected_tax = Tax::whereIn('id', $product_taxes->pluck('tax_id'))->get();
        $unselected_tax = Tax::whereNotIn('id', $product_taxes->pluck('tax_id'))->get();

        return response()->json([
            'product' => $product,
            'selected_tax' => $selected_tax,
            'unselected_tax' => $unselected_tax
        ]);

    } // End Method
    // Product load get-product-for-invoice
    public function GetProductForInvoiceByBarcode(Request $request){
        
        $product_code = $request->barcode;
        $product = Product::where('product_code',$product_code)->first();
        $product_taxes = ProductTax::where('product_id', $product->id)->get();
        $selected_tax = Tax::whereIn('id', $product_taxes->pluck('tax_id'))->get();
        $unselected_tax = Tax::whereNotIn('id', $product_taxes->pluck('tax_id'))->get();
        $brand = Brand::where('id', $product->brand_id)->first();
        if($brand == null){
            $brand = '';
        }else{
            $brand = $brand->name;
        }
        return response()->json([
            'success' => true,
            'product' => $product,
            'selected_tax' => $selected_tax,
            'unselected_tax' => $unselected_tax,
            'brand' => $brand // Handle case if brand is null
        ]);

    } // End Method






}
