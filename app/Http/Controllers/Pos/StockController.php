<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\PurchasDetails;
use App\Models\Unit;
use Auth;
use Illuminate\support\Carbon;
use Image;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:stock-list'], ['only' => ['StockReport']]);
    }
    // Stock controller 
    public function StockReport(){
        $allData = Product::all();
        return view('backend.stock.stock_report', compact('allData'));

    }//End Method

    // Stock Report generation PDF
    public function StockReportPdf(){
        $allData = Product::all();
        return view('backend.stock.pdf.stock_report', compact('allData'));
    }//End Method


    // Stock Supplier and Product wise report
    public function StockSupplierWise(){

        $suppliers = Supplier::all();
        $brand = Brand::all();
        // $product = Product::all()
        return view('backend.stock.supplier_product_wise_report', compact('suppliers','brand'));

    }//End Method


    // Supplier Wise PDF Generating
    public function SupplierWisePdf(Request $request){
        // dd($request->all());
        $allData = PurchasDetails::where('supplier_id',$request->supplier_id)->orderBy('created_at','asc')->get();
        // dd($allData);
        return view('backend.pdf.supplier_wise_report_pdf', compact('allData'));

    }//End Method


    // Product Wise PDF Generating
    public function ProductWisePdf(Request $request){
        $product = Product::where('category_id',$request->category_id)->where('id',$request->product_id)->first();
        return view('backend.pdf.product_wise_report_pdf', compact('product'));

    }//End Method






}
