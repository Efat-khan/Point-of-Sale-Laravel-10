<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\ProductPriceCodes;
use App\Models\ProductTax;
use App\Models\Tax;
use App\Models\Unit;
use Auth;
use Illuminate\support\Carbon;
use Image;
use DB;
use Hamcrest\Type\IsString;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:product-list|product-create|product-edit|product-delete'], ['only' => ['ProductAll']]);
        $this->middleware(['permission:product-create'], ['only' => ['ProductAdd', 'ProductStore']]);
        $this->middleware(['permission:product-edit'], ['only' => ['ProductEdit', 'ProductUpdate']]);
        $this->middleware(['permission:product-delete'], ['only' => ['ProductDelete']]);
    }
    // Product show from database
    public function ProductAll()
    {
        $products = Product::latest()->get();
        // dd($products);
        return view('backend.product.product_all', compact('products'));
    } //End Method


    // Product insert form
    public function ProductAdd()
    {
        $supplier = Supplier::all();
        // $category = Category::all();
        $brands = Brand::all();
        $unit     = Unit::all();
        $tax     = Tax::all();
        $productPriceCode = ProductPriceCodes::all();
        return view('backend.product.product_add', compact('supplier', 'unit', 'brands','productPriceCode','tax'));
    } //End Method

    // Save Product insert form to Database
    public function ProductStore(Request $request)
    {
        $total_tax = 0;
        if($request->product_taxes != null)
        {
            if($request->product_taxes[0] != 'NoTax')
                {
                    foreach($request->product_taxes as $tax){
                        $total_tax += Tax::find($tax)->rate;
                    }
                }
        }
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'part_number' => 'nullable',
            'model_number' => 'nullable',
            'product_sort_name' => 'nullable|string|max:255',
            'brand_id' => 'integer|min:-1',
            'category_id' => 'integer|min:-1',
            'unit_id' => 'required|integer|min:-1',
            'supplier_id' => 'nullable|integer',
            'product_code' => 'nullable|string|max:50|unique:products,product_code',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'quantity' => 'required|numeric|min:0',
            'product_buying_price' => 'required|numeric|min:0',
            'product_selling_price' => 'required|numeric|min:0',
            'product_offer_price' => 'nullable|numeric|min:0',
            'product_price_code' => 'nullable|string|max:50',
            'db_com' => 'nullable|numeric|min:0',
            'market_com' => 'nullable|numeric|min:0',
            'special_com' => 'nullable|numeric|min:0',
            'barcode_type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if new brand needs to be created
        if ($request->brand_id == -1) {
            $brand = Brand::create([
                'name' => $request->brand_name,
                'created_by' => Auth::id(),
            ]);
            $brandName = $request->brand_name;
            $brandId = $brand->id;
        } else if($request->brand_id != 0) {
            $brandId = $request->brand_id;
            $brandName = Brand::find($brandId)->name;
        }else{
            $brandId = 0;
            $brandName = '';
        }

        // Check if new category needs to be created
        if ($request->category_id == -1) {
            $category = Category::create([
                'name' => $request->category_name,
                'brand_id' => $brandId,
                'created_by' => Auth::id(),
            ]);
            $categoryName = $request->category_name;
            $categoryId = $category->id;
        }  else if($request->category_id != 0) {
            $categoryId = $request->category_id;
            $categoryName = Category::find($categoryId)->name;
        }else{
            $categoryId = 0;
            $categoryName = '';
        }
        // Check if new unit needs to be created
        if ($request->unit_id == -1) {
            $unit = Unit::create([
                'name' => $request->unit_name,
                'created_by' => Auth::id(),
            ]);
            $unitId = $unit->id;
        } else {
            $unitId = $request->unit_id;
        }
        // SKU
        if (($request->brand_id != 0 && $request->category_id != 0)||($request->brand_id == -1 && $request->category_id == -1 || $request->category_id == -1)) {
                $sku = Str::upper(substr($brandName, 0, 2)) . '-' . Str::upper(substr($categoryName, 0, 2)) . '-' . $request->sku;
            
        } else {
            $sku = $request->sku;
        }
        // Handle product image
        $productImage = $this->handleImageUpload($request->file('product_image'));

        // Insert product
        $product = Product::create([
            'name' => $request->name,
            'product_sort_name' => $request->product_sort_name,
            'part_number' => $request->part_number,
            'model_number' => $request->model_number,
            'product_image' => $productImage,
            'supplier_id' => $request->supplier_id,
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'unit_id' => $unitId,
            'quantity' => $request->quantity,
            'product_code' => $request->product_code,
            'sku' => $sku,
            'product_buying_price' => $request->product_buying_price??0,
            'product_price' => $request->product_selling_price??0,
            'product_offer_price' => $request->product_offer_price??0,
            'product_price_code' => $request->product_price_code??'null',
            'tax_type' => (!is_null($total_tax) && $total_tax != 0) ? 'Included' : 'TaxFree',
            'tax' => $total_tax ?? 0,
            'db_com' => $request->db_com,
            'market_com' => $request->market_com,
            'special_com' => $request->special_com,
            'barcode_type' => $request->barcode_type,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);
        if($request->product_taxes != null)
        {
            if($request->product_taxes[0] != 'TaxFree')
                {
                    foreach($request->product_taxes as $tax){
                        ProductTax::create([
                            'product_id' => $product->id,
                            'tax_id' => $tax,
                        ]);
                    }
                }
        }
        return redirect()->route('product.all')->with([
            'message' => 'Product Inserted Successfully',
            'alert-type' => 'success',
        ]);
    }

/**
 * Handles product image upload.
 */
    private function handleImageUpload($image)
    {
        if ($image) {
            $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(200, 200)->save('upload/product_images/' . $imageName);
            return 'upload/product_images/' . $imageName;
        }
        return 'upload/no_image.jpg';
    } //End Method



    // Product Edit form
    public function ProductEdit($id)
    {
        $supplier = Supplier::all();
        $category = Category::all();
        $brands   = Brand::all();
        $unit     = Unit::all();
        $productPriceCode = ProductPriceCodes::all();
        $product_taxes = ProductTax::where('product_id', $id)->get();
        $selected_tax = Tax::whereIn('id', $product_taxes->pluck('tax_id'))->get();
        $unselected_tax = Tax::whereNotIn('id', $product_taxes->pluck('tax_id'))->get();

        $product = Product::findOrFail($id);
        return view('backend.product.product_edit', compact('product', 'supplier', 'category', 'unit', 'brands','productPriceCode','selected_tax','unselected_tax'));
    } //End Method


    // Product Update data save to database
    public function ProductUpdate(Request $request)
    {
        // dd($request->all());
        $product = Product::findOrFail($request->id);
        if($product->tax_type != 'TaxFree') {
            // Delete existing taxes for the product
            ProductTax::where('product_id', $product->id)->delete();
        }
        $total_tax = 0;
        if($request->product_taxes != null)
        {
            if($request->product_taxes[0] != 'TaxFree')
                {
                    foreach($request->product_taxes as $tax){
                        $total_tax += Tax::find($tax)->rate;
                        ProductTax::create([
                            'product_id' => $product->id,
                            'tax_id' => $tax,
                        ]);
                    }
                }
        }

        $updateData = [
            'name' => $request->name,
            'product_sort_name' => $request->product_sort_name,
            'part_number' => $request->part_number,
            'model_number' => $request->model_number,
            'supplier_id' => $request->supplier_id,
            'quantity' => $request->quantity,
            'product_code' => $request->product_code,
            'sku' => $request->sku,
            'product_buying_price' => $request->product_buying_price??0,
            'product_price' => $request->product_selling_price??0,
            'product_offer_price' => $request->product_offer_price??0,
            'product_price_code' => $request->product_price_code,
            'tax_type' => (!is_null($total_tax) && $total_tax != 0) ? 'Included' : 'TaxFree',
            'tax' => $total_tax ?? 0,
            'db_com' => $request->db_com,
            'market_com' => $request->market_com,
            'special_com' => $request->special_com,
            'barcode_type' => $request->barcode_type,
            'description' => $request->description,
            'updated_by' => optional(Auth::user())->id,
            'updated_at' => now(),
        ];
        // Check if new brand needs to be created
        if ($request->brand_id == -1) {
            $brand = Brand::create([
                'name' => $request->brand_name,
                'created_by' => Auth::id(),
            ]);
            $brandName = $request->brand_name;
            $brandId = $brand->id;
        } else if($request->brand_id != 0) {
            $brandId = $request->brand_id;
            $brandName = Brand::find($brandId)->name;
        }else{
            $brandId = 0;
            $brandName = '';
        }
        $updateData['brand_id'] = $brandId;
        // Check if new category needs to be created
        if ($request->category_id == -1) {
            $category = Category::create([
                'name' => $request->category_name,
                'brand_id' => $brandId,
                'created_by' => Auth::id(),
            ]);
            $categoryName = $request->category_name;
            $categoryId = $category->id;
        }  else if($request->category_id != 0) {
            $categoryId = $request->category_id;
            $categoryName = Category::find($categoryId)->name;
        }else{
            $categoryId = 0;
            $categoryName = '';
        }
        $updateData['category_id'] = $categoryId;
        // Check if new unit needs to be created
        if ($request->unit_id == -1) {
            $unit = Unit::create([
                'name' => $request->unit_name,
                'created_by' => Auth::id(),
            ]);
            $unitId = $unit->id;
        } else {
            $unitId = $request->unit_id;
        }
        $updateData['unit_id'] = $unitId;
        //handle sku update
        if(is_string($request->sku)){

            $skuParts = explode('-', $request->sku);
            $lastDigitsOfSKU = end($skuParts); // Gets the last element after the last '-'
            
        }

        if (($request->brand_id != 0 && $request->category_id != 0) || ($request->brand_id == -1 && $request->category_id == -1 || $request->category_id == -1)) {
            $sku = Str::upper(substr($brandName, 0, 2)) . '-' . Str::upper(substr($categoryName, 0, 2)) . '-' . $lastDigitsOfSKU;
        } else {
            $sku = $lastDigitsOfSKU;
        }

        $updateData['sku'] = $sku;
        // Handle product image
        if ($request->hasFile('product_image')) {
            $image = $request->file('product_image');
            $imageName = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $imagePath = 'upload/product_images/' . $imageName;

            Image::make($image)->resize(200, 200)->save(public_path($imagePath));

            $updateData['product_image'] = $imagePath;
        }

        $product->update($updateData);
        
        return redirect()->route('product.all')->with([
            'message' => $request->hasFile('product_image') ? 'Product Updated Successfully' : 'Product Updated Successfully without image',
            'alert-type' => 'success'
        ]);
    }
 //End Method

    // Product Delete from database
    public function ProductDelete($id)
    {
        $products = Product::findOrFail($id);
        $img =  $products->product_image;

        if ($img != 'upload/no_image.jpg') {
            unlink($img);
        }

        Product::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Product Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } //End Method
}
