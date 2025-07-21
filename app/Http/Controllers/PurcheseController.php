<?php

namespace App\Http\Controllers;

use App\Models\AdditionalFee;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductPriceCodes;
use App\Models\Supplier;
use App\Models\SupplierPurchese;
use App\Models\SupplierPurcheseDetails;
use App\Models\SupplierPurchesePayment;
use App\Models\SupplierPurchesePaymentDetails;
use App\Models\Tax;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PurcheseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:purchase-create'], ['only' => ['PurcheseAdd', 'PurchaseStore']]);
        $this->middleware(['permission:purchase-edit'], ['only' => ['PurchaseEdit', 'PurchaseUpdate']]);
        $this->middleware(['permission:purchase-delete'], ['only' => ['PurchaseDelete']]);
        $this->middleware(['permission:supplier-transaction-list'], ['only' => ['PurchesePayment', 'SupplierAllTransaction']]);
        $this->middleware(['permission:supplier-transaction-create'], ['only' => ['getPurchasesBySupplier', 'PurchasePaymentMakePayment']]);
        $this->middleware(['permission:supplier-transaction-edit'], ['only' => ['SupplierTransactionEdit','SupplierTransactionUpdate']]);
    }
    public function PurchaseAll(Request $request)
    {
        $all_suppliers = Supplier::all();
        $show_start_date = $request->get('startDate');
        $show_end_date = $request->get('endDate');
        $filter = $request->get('filter');
        $supplier_filter = $request->get('supplier_filter');

        // Handle date range
        if ($request->get('startDate') && $request->get('endDate')) {
            $startDate = Carbon::parse($request->get('startDate'));
            $endDate = Carbon::parse($request->get('endDate'))->endOfDay();
        } else {
            $startDate = Carbon::parse(today())->subDays(30)->startOfDay();
            $endDate = Carbon::parse(today())->endOfDay();
        }

        // Build base query
        $allDataQuery = SupplierPurchese::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '1')
            ->orderBy('created_at', 'desc');

        // Apply Supplier filter
        if ($supplier_filter != null) {
            $find_supplier_purchase = SupplierPurchesePayment::where('supplier_id', $supplier_filter)->pluck('purchase_id')->toArray();
            if (!empty($find_supplier_purchase)) {
                $allDataQuery->whereIn('id', $find_supplier_purchase);
            } else {
                // If no purchase found for customer, return empty collection
                $allDataQuery->whereRaw('0 = 1');
            }
        }

        // Final query execution
        $allData = $allDataQuery->get();

        // Get filtered purchase IDs from the main purchase query
        $filtered_purchase_ids = $allData->pluck('id')->toArray();

        // Filter payments based on purchases
        $payment = SupplierPurchesePayment::whereIn('purchase_id', $filtered_purchase_ids)->get();

        $total_amount = 0;
        $total_discount = 0;
        $total_due = 0;
        $total_paid = 0;

        foreach ($payment as $pay) {
            $total_amount += $pay->total_amount;
            $total_discount += $pay->discount_amount;
            $total_due += $pay->due_amount;
            $total_paid += $pay->paid_amount;
        }

        // Filter purchase details based on filtered purchase IDs
        $purchase_details = SupplierPurcheseDetails::whereIn('purchase_id', $filtered_purchase_ids)->get();

        $total_profit = 0;
        $total_qty = 0;
        $total_selling_price = 0;
        $total_buying_price = 0;

        foreach ($purchase_details as $value) {
            $total_selling_price += $value->selling_price;
            $total_buying_price += $value->buying_price;
            $total_qty += $value->selling_qty;
        }

        return response()->view('backend.purchese.purchase_all', compact(
            'allData',
            'all_suppliers',
            'filter',
            'show_start_date',
            'show_end_date',
            'startDate',
            'endDate',
            'supplier_filter',
            'total_amount',
            'total_profit',
            'total_paid',
            'total_due'
        ));
    }
    // Purchase All Page Print filter wise 
    public function PurchaseAllFilterPrint($startDate = null, $endDate = null, $filter = 'null', $supplier_filter = 'null')
    {
        $show_start_date = $startDate;
        $show_end_date = $endDate;

        // Handle date range
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subDays(30)->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        // Build base query
        $allDataQuery = SupplierPurchese::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '1')
            ->orderByDesc('created_at');
        // Apply customer filter
        if ($supplier_filter !== 'null') {
            $purchaseIds = SupplierPurchesePayment::where('supplier_id', $supplier_filter)->pluck('purchase_id');

            if ($purchaseIds->isNotEmpty()) {
                $allDataQuery->whereIn('id', $purchaseIds);
            } else {
                // No matching purchases, return empty result
                $allDataQuery->whereRaw('0 = 1');
            }
        }

        $allData = $allDataQuery->get();

        // Get filtered purchase IDs
        $filteredPurchaseIds = $allData->pluck('id');

        // Payment details
        $payments = SupplierPurchesePayment::whereIn('purchase_id', $filteredPurchaseIds)->get();

        $total_amount = $payments->sum('total_amount');
        $total_discount = $payments->sum('discount_amount');
        $total_due = $payments->sum('due_amount');
        $total_paid = $payments->sum('paid_amount'); // You were calculating it but not returning/using it
        return response()->view('backend.purchese.pdf.purchase_all_pdf', compact(
            'allData',
            'filter',
            'show_start_date',
            'show_end_date',
            'startDate',
            'endDate',
            'supplier_filter',
            'total_amount',
            'total_due'
        ));
    }
    // Invoice insert form
    public function PurcheseAdd()
    {
        $suppliers = Supplier::get();
        $products = Product::latest()->get();
        $date = date('Y-m-d');
        $tax = Tax::orderBy('id', 'desc')->get();
        $additional_fees = AdditionalFee::orderBy('id', 'desc')->get();
        $supplier_purchase_data = SupplierPurchese::orderBy('id', 'desc')->first();
        if ($supplier_purchase_data == null) {
            $firstReg = '0';
            $purchase_no = $firstReg + 1;
        } else {
            $supplier_purchase_data = SupplierPurchese::orderBy('id', 'desc')->first()->id;
            $purchase_no = $supplier_purchase_data + 1;
        }
        // dd($purchase_no);
        return view('backend.purchese.purchese_add', compact('purchase_no', 'suppliers', 'products', 'date', 'tax', 'additional_fees'));
    } //End Method
    // Purchase Store
    public function PurchaseStore(Request $request)
    {
        // dd($request->all());
        // Validate input
        $request->validate([
            'purchase_no' => 'required',
            'date' => 'required|date',
            'product_id' => 'required|array',
            'buying_price' => 'required|array',
            'estimated_amount' => 'required|numeric',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        // Validate paid amount
        if ($request->paid_amount > $request->total) {
            return redirect()->back()->with([
                'message' => 'Sorry, paid amount is greater than the total price.',
                'alert-type' => 'error'
            ]);
        }

        // Transaction ensures atomicity
        $purchase_id = DB::transaction(function () use ($request) {
            if ($request->saveBtn == 1) {
                $purchase_type = 'purchase';
            } else {
                $purchase_type = 'draft';
            }
            // Create Invoice
            $supplier_purchase = SupplierPurchese::create([
                'purchase_no' => $request->purchase_no,
                'purchase_type' => $purchase_type,
                'dn_no' => $request->purchase_no,
                'wo_no' => $request->wo_no,
                'date' => date('Y-m-d', strtotime($request->date)),
                'description' => $request->description,
                'purchase_tax_type' => json_encode($request->total_taxes ?? []),
                'purchase_tax_amount' => $request->purchase_tax_amount,
                'purchase_discount_type' => $request->discount_status,
                'purchase_discount_rate' => ($request->discount_status === 'fixed_discount' ? $request->discount_show : $request->discount_amount),
                'purchase_discount_amount' => ($request->discount_status === 'fixed_discount' ? $request->discount_amount : $request->discount_show),
                'additional_charge_type' => json_encode($request->total_additional_fees_type ?? []),
                'additional_charge_amount' => $request->total_additional_fees_amount,
                'status' => '1', // Change this to '0' if approval is required
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);

            $total_buying_price = 0;

            // Insert Invoice Details & Update Product Stock
            foreach ($request->product_id as $index => $productId) {
                $buying_price = $request->buying_price[$index];
                $total_buying_price += $buying_price;
                SupplierPurcheseDetails::create([
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'purchase_id' => $supplier_purchase->id,
                    'category_id' => $request->category_id[$index],
                    'product_id' => $productId,
                    'buying_qty' => $request->buying_qty[$index],
                    'product_buying_price' => $request->product_buying_price[$index],
                    'buying_price' => $buying_price,
                    'discount_type' => $request->discount_rate[$index] == $request->discount_amount_per_product[$index] ? 'fixed' : 'percentage',
                    'discount_rate' => $request->discount_rate[$index],
                    'discount_amount' => $request->discount_amount_per_product[$index],
                    'tax_type' => json_encode($request->product_tax[$productId] ?? []),
                    'tax_amount' => $request->product_tax_amount[$index],
                    'status' => '1',
                    'created_at' => Carbon::now(),
                ]);

                // Update Product Stock
                if ($purchase_type !== 'draft'){
                    Product::where('id', $productId)->increment('quantity', $request->buying_qty[$index]);
                }
            }
            // Handle New or Existing Customer
            $supplier_id = ($request->supplier_id == '0')
                ? Supplier::create([
                    'name' => $request->name,
                    'mobile_no' => $request->mobile_no,
                    'email' => $request->email,
                    'office_address' => $request->office_address,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ])->id
                : $request->supplier_id;

            // Create Payment Record
            $payment = SupplierPurchesePayment::create([
                'purchase_id' => $supplier_purchase->id,
                'supplier_id' => $supplier_id,
                'paid_status' => $request->paid_status,
                'total_amount' => $request->total,
                'discount_amount' => $request->total_discount_amount,
                'paid_amount' => $request->paid_status == 'full-paid' ? $request->total : ($request->paid_status == 'partial-paid' ? $request->paid_amount : 0),
                'due_amount' => $request->paid_status == 'full-paid' ? 0 : $request->total - ($request->paid_status == 'partial-paid' ? $request->paid_amount : 0),
                'total_tax_amount' => $request->tax_value,
                'total_additional_charge_amount' => $request->total_additional_fees_amount,
            ]);

            // Create Payment Detail Record
            SupplierPurchesePaymentDetails::create([
                'supplier_id' => $supplier_id,
                'transaction_id' => 'TXN-' . strtoupper(Str::random(4)) . '-' . Str::uuid(),
                'purchase_id' => $supplier_purchase->id,
                'date' => date('Y-m-d', strtotime($request->date)),
                'current_paid_amount' => $payment->paid_amount,
                'received_by' => Auth::user()->name
            ]);
            return $supplier_purchase->id;  // Return Invoice ID

        });

        // Fetch Purchase after transaction
        $purchase = SupplierPurchese::with('supplier_purchese_details')->findOrFail($purchase_id);

        // Handle Save Button Logic
        $redirect = 'purchase-all';
        switch ($request->saveBtn) {
            case 1:
                $pre_due = SupplierPurchese::whereHas('supplier_purchese_payment', function ($q) use ($purchase) {
                    $q->where('supplier_id', $purchase->supplier_purchese_payment->supplier_id);
                })
                    ->where('id', '!=', $purchase->id)
                    ->where('status', 1)
                    ->withSum('supplier_purchese_payment', 'due_amount')
                    ->get()
                    ->sum('payment_sum_due_amount');

                return view('backend.purchese.pdf.purchase_print', compact('purchase', 'pre_due', 'redirect'));
            case 0:
                return redirect()->route('purchase.add')->with([
                    'message' => 'Purchase Saved Successfully',
                    'alert-type' => 'success'
                ]);
            default:
                return redirect()->route('purchase.add')->with([
                    'message' => 'Purchase Saved Successfully',
                    'alert-type' => 'success'
                ]);
        }
    }
    public function PurchaseEdit($id)
    {
        $brands = Brand::all();
        $category = Category::all();
        $suppliers = Supplier::all();
        $products = Product::latest()->get();
        $tax = Tax::orderBy('id', 'desc')->get();
        $purchase = SupplierPurchese::findOrFail($id);
        $payment = SupplierPurchesePayment::with('supplier')->where('purchase_id', $purchase->id)->first();
        return view('backend.purchese.purchase_edit', compact('brands', 'category', 'suppliers', 'products', 'tax', 'purchase', 'payment'));
    }
    // Purchase Update Start
    public function PurchaseUpdate(Request $request)
    {
        // dd($request->all());
        // Validate input
        $request->validate([
            'purchase_no' => 'required',
            'date' => 'required|date',
            'product_id' => 'required|array',
            'purchase_details_id' => 'required|array',
            'buying_qty' => 'required|array',
            'product_buying_price' => 'required|array',
            'product_buying_price' => 'required|array',
            'estimated_amount' => 'required|numeric',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        // Validate paid amount
        if ($request->paid_amount > $request->total) {
            return redirect()->back()->with([
                'message' => 'Sorry, paid amount is greater than the total price.',
                'alert-type' => 'error'
            ]);
        }

        DB::transaction(function () use ($request) {
            //update purchase
            $purchase = SupplierPurchese::findOrFail($request->purchase_no);
            $old_purchase_type = $purchase->purchase_type;
            if ($request->saveBtn == 2) {
                $purchase_type = 'purchase';
            } else {
                $purchase_type = $old_purchase_type;
            }
            $purchase->update([
                'purchase_no' => $request->purchase_no,
                'dn_no' => $request->purchase_no,
                'wo_no' => $request->wo_no,
                'purchase_type' => $purchase_type,
                'date' => date('Y-m-d', strtotime($request->date)),
                'description' => $request->description,
                'purchase_tax_type' => json_encode($request->total_taxes ?? []),
                'purchase_tax_amount' => $request->purchase_tax_amount,
                'purchase_discount_type' => $request->discount_status,
                'purchase_discount_rate' => ($request->discount_status === 'fixed_discount' ? $request->discount_show : $request->discount_amount),
                'purchase_discount_amount' => ($request->discount_status === 'fixed_discount' ? $request->discount_amount : $request->discount_show),
                'additional_charge_type' => json_encode($request->total_additional_fees_type ?? []),
                'additional_charge_amount' => $request->total_additional_fees_amount,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            $existingDetails = SupplierPurcheseDetails::where('purchase_id', $request->purchase_no)->get()->keyBy('id');

            $submittedIds = collect($request->purchase_details_id)->filter(fn($id) => $id > 0)->values();

            // Handle deleted items
            foreach ($existingDetails as $detailId => $detail) {
                if (!$submittedIds->contains($detailId)) {
                    if ($old_purchase_type !== 'draft') {
                        Product::where('id', $detail->product_id)->decrement('quantity', $detail->buying_qty); // restore stock
                    }
                    $detail->delete();
                }
            }

            $total_product = count($request->product_id);

            for ($index = 0; $index < $total_product; $index++) {
                $productId = $request->product_id[$index];
                $detailId = $request->purchase_details_id[$index];
                $buyingQty = $request->buying_qty[$index];
                $buyingPrice = $request->buying_price[$index];

                $data = [
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'purchase_id' => $purchase->id,
                    'category_id' => $request->category_id[$index],
                    'product_id' => $productId,
                    'buying_qty' => $buyingQty,
                    'product_buying_price' => $request->product_buying_price[$index],
                    'buying_price' => $buyingPrice,
                    'discount_type' => $request->discount_rate[$index] == $request->discount_amount_per_product[$index] ? 'fixed' : 'percentage',
                    'discount_rate' => $request->discount_rate[$index],
                    'discount_amount' => $request->discount_amount_per_product[$index],
                    'tax_type' => json_encode($request->product_tax[$productId] ?? []),
                    'tax_amount' => $request->product_tax_amount[$index],
                    'status' => '1',
                    'updated_at' => Carbon::now(),
                ];

                if ($detailId == 0) {
                    SupplierPurcheseDetails::create($data);
                    if ($request->saveBtn != 0 && $old_purchase_type === 'draft') {
                        Product::where('id', $productId)->increment('quantity', $buyingQty);
                    }
                    if ($old_purchase_type === 'purchase') {
                        Product::where('id', $productId)->increment('quantity', $buyingQty);
                    }
                } else {
                    $existingDetail = $existingDetails[$detailId];
                    $oldQty = $existingDetail->buying_qty;
                    $qtyDiff = $buyingQty - $oldQty;

                    $existingDetail->update($data);
                    if ($request->saveBtn != 0 && $old_purchase_type === 'draft') {
                        Product::where('id', $productId)->increment('quantity', $buyingQty);
                    }
                    if ($old_purchase_type === 'purchase') {
                        Product::where('id', $productId)->increment('quantity', $qtyDiff);
                    }
                }
            }

            $payment = SupplierPurchesePayment::where('purchase_id', $purchase->id)->first();

            $supplier_id = $request->supplier_id;

            if ($supplier_id == 0) {
                $supplier_id = Supplier::create([
                    'name' => $request->name,
                    'mobile_no' => $request->mobile_no,
                    'email' => $request->email,
                    'address' => $request->address,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ])->id;
            }
            $payment->update([
                'supplier_id' => $supplier_id,
                'paid_status' => $request->paid_status,
                'discount_amount' => $request->total_discount_amount,
                'total_amount' => $request->total,
                'paid_amount' => $request->paid_status == 'full-paid' ? $request->total : ($request->paid_status == 'partial-paid' ? $request->paid_amount : 0),
                'due_amount' => $request->paid_status == 'full-paid' ? 0 : $request->total - ($request->paid_status == 'partial-paid' ? $request->paid_amount : 0),
                'total_tax_amount' => $request->tax_value,
                'total_additional_charge_amount' => $request->total_additional_fees_amount,
            ]);

            if ($old_purchase_type === 'draft' && $purchase_type === 'purchase') {
                SupplierPurchesePaymentDetails::create([
                    'supplier_id' => $supplier_id,
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(4)) . '-' . Str::uuid(),
                    'purchase_id' => $purchase->id,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'current_paid_amount' => $payment->paid_amount,
                    'received_by' => Auth::user()->name
                ]);
            } elseif ($old_purchase_type !== 'draft') {
                $payment_details = SupplierPurchesePaymentDetails::where('purchase_id', $purchase->id)->first();

                $payment_details->update(
                    [
                        'supplier_id' => $supplier_id,
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'current_paid_amount' => $payment->paid_amount,
                    ]
                );
            }
            // dd('ok');
        });
        // Handle Save Button Logic
        $purchase = SupplierPurchese::findOrFail($request->purchase_no);
        $redirect = 'purchase-edit';
        switch ($request->saveBtn) {
            case 2:
                $pre_due = SupplierPurchese::whereHas('supplier_purchese_payment', function ($q) use ($purchase) {
                    $q->where('supplier_id', $purchase->supplier_purchese_payment->supplier_id);
                })
                    ->where('id', '!=', $purchase->id)
                    ->where('status', 1)
                    ->withSum('supplier_purchese_payment', 'due_amount')
                    ->get()
                    ->sum('payment_sum_due_amount');

                return view('backend.purchese.pdf.purchase_print', compact('purchase', 'pre_due', 'redirect'));
            case 0:
            default:
                return redirect()->route('purchase.edit', $request->purchase_no)->with([
                    'message' => 'Purchase Updated Successfully.',
                    'alert-type' => 'success'
                ]);
        }
    }
    //Purchase Update End
    // Invoice Delete 
    public function PurchaseDelete($id)
    {
        $purchase = SupplierPurchese::findOrFail($id);
        $purchase->delete();

        SupplierPurcheseDetails::where('purchase_id', $purchase->id)->delete();
        SupplierPurchesePayment::where('purchase_id', $purchase->id)->delete();
        SupplierPurchesePaymentDetails::where('purchase_id', $purchase->id)->delete();

        $notification = array(
            'message' => 'Purchase Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } //End Method
    // Supplier wise due payment Start
    public function PurchesePayment()
    {
        $supplier_purchase_due_and_payment_infos = Supplier::select('suppliers.id', 'suppliers.name', 'suppliers.mobile_no')
            ->selectRaw('SUM(supplier_purchese_payments.paid_amount) as total_paid')
            ->selectRaw('SUM(supplier_purchese_payments.due_amount) as total_due')
            ->selectRaw('SUM(supplier_purchese_payments.total_amount) as total_amount')
            ->join('supplier_purchese_payments', 'suppliers.id', '=', 'supplier_purchese_payments.supplier_id')
            ->where('supplier_purchese_payments.due_amount', '!=', 0)
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.mobile_no')
            ->get();
        // dd($customer_due_and_payment_infos);
        return view('backend.purchese.supplier_wise_purchese_due_payment', compact('supplier_purchase_due_and_payment_infos'));
    }
    // Supplier wise due payment End
    // Get Supplier wise Purchase list Start
    public function getPurchasesBySupplier($id)
    {
        $purchases = SupplierPurchesePayment::where('supplier_id', $id)->where('due_amount', '!=', 0)
            ->select('purchase_id', 'due_amount')->orderBy('due_amount', 'desc')
            ->get();
        return response()->json($purchases);
    }
    // Supplier Payment Start
    public function PurchasePaymentMakePayment(Request $request)
    {
        // dd($request->all());
        $new_paid_amount = $request->paid_amount;
        $transaction_id = 'TXN-' . strtoupper(Str::random(4)) . '-' . Str::uuid();

        if($request->purchase_type === 'all'){
            if ($request->due_amount < $new_paid_amount) {
                $notification = array(
                    'message' => 'Paid amount is more than due amount!',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            } 
            $supplier_wise_due = SupplierPurchesePayment::where('supplier_id', $request->supplier_id)
                ->where('paid_status', '!=', 'full-paid')
                ->orderBy('due_amount', 'desc')->get();
            //dd($customer_wise_due);
            foreach ($supplier_wise_due as $due) {
                if($new_paid_amount != 0){
    
                    $payment = SupplierPurchesePayment::where('purchase_id', $due->purchase_id)->first();
                    $payment_details = new SupplierPurchesePaymentDetails();
                    if($new_paid_amount>=$due->due_amount){
                        $payment->paid_status = 'full-paid';
                        $payment->paid_amount += $due->due_amount;
                        $payment->due_amount = 0;
                        
                        // payment details
                        $payment_details->transaction_id = $transaction_id;
                        $payment_details->current_paid_amount = $due->due_amount;
                        $payment_details->purchase_id = $due->purchase_id;    
    
                        $payment_details->supplier_id = $request->supplier_id;
                        $payment_details->date = date('Y-m-d', strtotime($request->date));
                        $payment_details->payment_type = $request->payment_type;
                        // Bank related payments
                        $payment_details->bank_name = $request->bank_name;
                        $payment_details->bank_branch_name = $request->bank_branch_name;
                        $payment_details->bank_cheque_number = $request->bank_cheque_number;
                        $payment_details->bank_account_number = $request->bank_account_number;
                        $payment_details->bank_micr_code = $request->bank_micr_code;
                        $payment_details->bank_check_issue_date = !empty($request->bank_check_issue_date)? date('Y-m-d', strtotime($request->bank_check_issue_date)):null;
                        $payment_details->bank_check_cleared_at = !empty($request->bank_check_cleared_at)?date('Y-m-d', strtotime($request->bank_check_cleared_at)):null;
                        //online payment related
                        $payment_details->sender_account_number = $request->sender_account_number;
                        $payment_details->receiver_account_number = $request->receiver_account_number;
                        $payment_details->online_transaction_id = $request->online_transaction_id;
                        $payment_details->online_transfer_method = $request->online_transfer_method;
                        //mobile payment related
                        $payment_details->mobile_banking_type = $request->mobile_banking_type;
                        $payment_details->mobile_banking_account_type = $request->mobile_banking_account_type;
                        $payment_details->mobile_banking_sender_number = $request->mobile_banking_sender_number;
                        $payment_details->mobile_banking_receiver_number = $request->mobile_banking_receiver_number;
                        $payment_details->mobile_banking_transaction_id = $request->mobile_banking_transaction_id;
    
                        $payment_details->status = $request->status ;               
                        $payment_details->payee_name = $request->payee_name;
                        $payment_details->received_by = $request->received_by;
                        $payment_details->remarks = $request->remarks;
    
                        $payment->save();
                        $payment_details->save();
    
                        $new_paid_amount -= $due->due_amount;
                    }elseif($new_paid_amount<$due->due_amount){
    
                        $payment->paid_status = 'partial-paid';
                        $payment->paid_amount += $new_paid_amount;
                        $payment->due_amount -= $new_paid_amount;
                        $payment->save();
    
                        //payment details
                        $payment_details->transaction_id = $transaction_id;
                        $payment_details->current_paid_amount = $new_paid_amount;
                        $payment_details->purchase_id = $due->purchase_id;
    
                        $payment_details->supplier_id = $request->supplier_id;
                        $payment_details->date = date('Y-m-d', strtotime($request->date));
                        $payment_details->payment_type = $request->payment_type;
                        // Bank related payments
                        $payment_details->bank_name = $request->bank_name;
                        $payment_details->bank_branch_name = $request->bank_branch_name;
                        $payment_details->bank_cheque_number = $request->bank_cheque_number;
                        $payment_details->bank_account_number = $request->bank_account_number;
                        $payment_details->bank_micr_code = $request->bank_micr_code;
                        $payment_details->bank_check_issue_date = !empty($request->bank_check_issue_date)? date('Y-m-d', strtotime($request->bank_check_issue_date)):null;
                        $payment_details->bank_check_cleared_at = !empty($request->bank_check_cleared_at)?date('Y-m-d', strtotime($request->bank_check_cleared_at)):null;
                        //online payment related
                        $payment_details->sender_account_number = $request->sender_account_number;
                        $payment_details->receiver_account_number = $request->receiver_account_number;
                        $payment_details->online_transaction_id = $request->online_transaction_id;
                        $payment_details->online_transfer_method = $request->online_transfer_method;
                        //mobile payment related
                        $payment_details->mobile_banking_type = $request->mobile_banking_type;
                        $payment_details->mobile_banking_account_type = $request->mobile_banking_account_type;
                        $payment_details->mobile_banking_sender_number = $request->mobile_banking_sender_number;
                        $payment_details->mobile_banking_receiver_number = $request->mobile_banking_receiver_number;
                        $payment_details->mobile_banking_transaction_id = $request->mobile_banking_transaction_id;
    
                        $payment_details->status = $request->status ;               
                        $payment_details->payee_name = $request->payee_name;
                        $payment_details->received_by = $request->received_by;
                        $payment_details->remarks = $request->remarks;
                        $payment_details->updated_by = Auth::user()->id;
                        $payment_details->save();
    
                        $new_paid_amount = 0;
                        break;
                    }
                }
            }
        } else {
            $payment = SupplierPurchesePayment::where('purchase_id', $request->purchase_type)->first();
            if ($payment->due_amount < $new_paid_amount) {
                $notification = array(
                    'message' => 'Paid amount is more than due amount of Purchase No:'.$request->purchase_type,
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
            $payment_details = new SupplierPurchesePaymentDetails();
            if ($new_paid_amount == $payment->due_amount) {

                $payment->paid_status = 'full-paid';
                $payment->paid_amount += $payment->due_amount;
                $payment->due_amount = 0;
            } elseif ($new_paid_amount < $payment->due_amount) {
                $payment->paid_status = 'partial-paid';
                $payment->paid_amount += $new_paid_amount;
                $payment->due_amount -= $new_paid_amount;
            }

            // payment details
            $payment_details->transaction_id = $transaction_id;
            $payment_details->current_paid_amount = $new_paid_amount;
            $payment_details->purchase_id = $payment->purchase_id;

            $payment_details->supplier_id = $request->supplier_id;
            $payment_details->date = date('Y-m-d', strtotime($request->date));
            $payment_details->payment_type = $request->payment_type;
            // Bank related payments
            $payment_details->bank_name = $request->bank_name;
            $payment_details->bank_branch_name = $request->bank_branch_name;
            $payment_details->bank_cheque_number = $request->bank_cheque_number;
            $payment_details->bank_account_number = $request->bank_account_number;
            $payment_details->bank_micr_code = $request->bank_micr_code;
            $payment_details->bank_check_issue_date = !empty($request->bank_check_issue_date) ? date('Y-m-d', strtotime($request->bank_check_issue_date)) : null;
            $payment_details->bank_check_cleared_at = !empty($request->bank_check_cleared_at) ? date('Y-m-d', strtotime($request->bank_check_cleared_at)) : null;
            //online payment related
            $payment_details->sender_account_number = $request->sender_account_number;
            $payment_details->receiver_account_number = $request->receiver_account_number;
            $payment_details->online_transaction_id = $request->online_transaction_id;
            $payment_details->online_transfer_method = $request->online_transfer_method;
            //mobile payment related
            $payment_details->mobile_banking_type = $request->mobile_banking_type;
            $payment_details->mobile_banking_account_type = $request->mobile_banking_account_type;
            $payment_details->mobile_banking_sender_number = $request->mobile_banking_sender_number;
            $payment_details->mobile_banking_receiver_number = $request->mobile_banking_receiver_number;
            $payment_details->mobile_banking_transaction_id = $request->mobile_banking_transaction_id;

            $payment_details->status = $request->status;
            $payment_details->payee_name = $request->payee_name;
            $payment_details->received_by = $request->received_by;
            $payment_details->remarks = $request->remarks;
            $payment_details->updated_by = Auth::user()->id;
            $payment->save();
            $payment_details->save();
        }

        $notification = array(
            'message' => 'Payment make successfully!',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // Supplier Payment End
    // Get Supplier wise Purchase list Start
    // Direct Purchase print from Add Purchase page
    public function PurchasePosPrint($id)
    {
        $purchase = SupplierPurchese::with('supplier_purchese_details')->findOrFail($id);
        // dd($purchase);
        $supplier_id = SupplierPurchesePayment::where('purchase_id', $id)->first()->supplier_id;
        // dd($supplier_id);
        $pre_due = SupplierPurchesePayment::where('supplier_id', $supplier_id)
            ->where('id', '!=', $id)
            ->sum('due_amount');
        // dd($pre_due);
        return view('backend.purchese.pdf.purchase_pos_print', compact('purchase', 'pre_due'));
    } // End Method
    

    // Supplier wise due report
    public function SupplierReport(Request $request)
    {
        // dd($request->all());
        $supplierId = $request->supplier_id;

        // Fetch all payments for the customer
        $allData = SupplierPurchesePayment::where('supplier_id', $supplierId)->get();
        $total_due = $allData->sum('due_amount');
        // Get customer info from first use Customer model if available
        $supplier = Supplier::where('id', $supplierId)->first();
        // Get distinct purchase IDs from payment records
        $purchaseIds = SupplierPurchesePayment::where('supplier_id', $supplierId)
            ->distinct()
            ->pluck('supplier_id');

        // Fetch and group payment details by created_at
        $paymentDetails = SupplierPurchesePaymentDetails::whereIn('supplier_id', $purchaseIds)->where('current_paid_amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->transaction_id;
            });

        // Prepare grouped report data
        $groupedDetails = [];

        foreach ($paymentDetails as $details) {
            $payment = $details[0]; // assume first entry represents group
            $commonData = [
                'created_at' => $payment->created_at,
                'transaction_id' => $payment->transaction_id,
                'total_amount' => $details->sum('current_paid_amount'),
                'purchase_ids' => implode(', ', $details->pluck('purchase_id')->toArray()),
                'payment_type' => $payment->payment_type == 'check_payment' ? 'Bank Payment' : ($payment->payment_type == 'mobile_banking' ? 'Mobile Banking' : ($payment->payment_type == 'online_transaction' ? 'Online Payment' : 'Cash Payment')),
                'status' => $payment->status == 'pending' ? 'Pending' : ($payment->status == 'paid' ? 'Paid' : ($payment->status == 'refunded' ? 'Refunded' : 'Rejected')),
                'payee_name' => $payment->payee_name,
                'received_by' => $payment->received_by,
                'remarks' => $payment->remarks,
            ];

            $paymentDetailsExtra = [];

            if ($payment->payment_type == 'check_payment') {
                $paymentDetailsExtra = [
                    'bank_name' => $payment->bank_name,
                    'bank_branch_name' => $payment->bank_branch_name,
                    'bank_account_number' => $payment->bank_account_number,
                    'bank_cheque_number' => $payment->bank_cheque_number,
                    'bank_micr_code' => $payment->bank_micr_code,
                    'bank_check_issue_date' => $payment->bank_check_issue_date,
                    'bank_check_cleared_at' => $payment->bank_check_cleared_at,
                    'bank_cheque_image' => $payment->bank_cheque_image,
                ];
            } elseif ($payment->payment_type == 'mobile_banking') {
                $paymentDetailsExtra = [
                    'mobile_banking_type' => $payment->mobile_banking_type,
                    'mobile_banking_account_type' => $payment->mobile_banking_account_type,
                    'mobile_banking_sender_number' => $payment->mobile_banking_sender_number,
                    'mobile_banking_receiver_number' => $payment->mobile_banking_receiver_number,
                    'mobile_banking_transaction_id' => $payment->mobile_banking_transaction_id,
                ];
            } elseif ($payment->payment_type == 'online_transaction') {
                $paymentDetailsExtra = [
                    'bank_name' => $payment->bank_name,
                    'bank_branch_name' => $payment->bank_branch_name,
                    'sender_account_number' => $payment->sender_account_number,
                    'receiver_account_number' => $payment->receiver_account_number,
                    'online_transaction_id' => $payment->online_transaction_id,
                    'online_transfer_method' => $payment->online_transfer_method,
                ];
            }

            $groupedDetails[] = array_merge($commonData, $paymentDetailsExtra);
        }
        // dd($groupedDetails);

        return view('backend.purchese.supplier_report', compact('allData', 'supplier','total_due', 'groupedDetails'));
    }
    public function SupplierPurchaseReportPdf($supplierId)
    {

        // Fetch all payments for the supplier
        $allData = SupplierPurchesePayment::where('supplier_id', $supplierId)->get();
        // Get supplier info from first use supplier model if available
        $supplier = Supplier::where('id', $supplierId)->first();
        return view('backend.purchese.pdf.supplier_purchase_report', compact('allData', 'supplier'));
    } // End Method
    public function SupplierTransactionsReportPdf($supplierId)
    {
        // Get supplier info from first use supplier model if available
        $supplier = Supplier::where('id', $supplierId)->first();
        // Get distinct invoice IDs from payment records
        $purchaseIds = SupplierPurchesePayment::where('supplier_id', $supplierId)
            ->distinct()
            ->pluck('purchase_id');

        // Fetch and group payment details by created_at
        $paymentDetails = SupplierPurchesePaymentDetails::whereIn('purchase_id', $purchaseIds)->where('current_paid_amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->transaction_id;
            });

        // Prepare grouped report data
        $groupedDetails = [];

        foreach ($paymentDetails as $details) {
            $payment = $details[0]; // assume first entry represents group
            $commonData = [
                'created_at' => $payment->created_at,
                'total_amount' => $details->sum('current_paid_amount'),
                'purchase_ids' => implode(', ', $details->pluck('purchase_id')->toArray()),
                'payment_type' => $payment->payment_type == 'check_payment' ? 'Bank Payment' : ($payment->payment_type == 'mobile_banking' ? 'Mobile Banking' : ($payment->payment_type == 'online_transaction' ? 'Online Payment' : 'Cash Payment')),
                'status' => $payment->status == 'pending' ? 'Pending' : ($payment->status == 'paid' ? 'Paid' : ($payment->status == 'refunded' ? 'Refunded' : 'Rejected')),
                'payee_name' => $payment->payee_name,
                'received_by' => $payment->received_by,
                'remarks' => $payment->remarks,
            ];

            $paymentDetailsExtra = [];

            if ($payment->payment_type == 'check_payment') {
                $paymentDetailsExtra = [
                    'bank_name' => $payment->bank_name,
                    'bank_branch_name' => $payment->bank_branch_name,
                    'bank_account_number' => $payment->bank_account_number,
                    'bank_cheque_number' => $payment->bank_cheque_number,
                    'bank_micr_code' => $payment->bank_micr_code,
                    'bank_check_issue_date' => $payment->bank_check_issue_date,
                    'bank_check_cleared_at' => $payment->bank_check_cleared_at,
                    'bank_cheque_image' => $payment->bank_cheque_image,
                ];
            } elseif ($payment->payment_type == 'mobile_banking') {
                $paymentDetailsExtra = [
                    'mobile_banking_type' => $payment->mobile_banking_type,
                    'mobile_banking_account_type' => $payment->mobile_banking_account_type,
                    'mobile_banking_sender_number' => $payment->mobile_banking_sender_number,
                    'mobile_banking_receiver_number' => $payment->mobile_banking_receiver_number,
                    'mobile_banking_transaction_id' => $payment->mobile_banking_transaction_id,
                ];
            } elseif ($payment->payment_type == 'online_transaction') {
                $paymentDetailsExtra = [
                    'bank_name' => $payment->bank_name,
                    'bank_branch_name' => $payment->bank_branch_name,
                    'sender_account_number' => $payment->sender_account_number,
                    'receiver_account_number' => $payment->receiver_account_number,
                    'online_transaction_id' => $payment->online_transaction_id,
                    'online_transfer_method' => $payment->online_transfer_method,
                ];
            }

            $groupedDetails[] = array_merge($commonData, $paymentDetailsExtra);
        }
        // dd($groupedDetails);

        return view('backend.purchese.pdf.supplier_transactions_report', compact('supplier', 'groupedDetails'));
    } // End Method

    //edit purches report start
    // Supplier wise purchase report
    public function SupplierPurchaseReport($purchase_id)
    {
        $suppliers = Supplier::get();
        $productPriceCode = ProductPriceCodes::all();
        $supplier_purchase = SupplierPurchese::with('supplier_purchese_details')->findOrFail($purchase_id);
        $supplier_purchase_payment = SupplierPurchesePayment::where('purchase_id', $purchase_id)->first();
        // dd($supplier_purchese_payment);
        return view('backend.purchese.supplier_purchase_report', compact('supplier_purchase_payment', 'supplier_purchase', 'productPriceCode', 'suppliers'));
    }
    // Edit Invoice Edit file
    public function supplier_purchase_data($id)
    {
        $supplier_purchese = SupplierPurchese::with('supplier_purchese_details.product', 'supplier_purchese_details.category', 'supplier_purchese_payment')->findOrFail($id);
        return response($supplier_purchese);
    }



    // Supplier wise payment list
    public function SupplierWiseAllReport()
    {
        // dd("ok");
        $supplier_purchase_and_payment_infos = Supplier::select('suppliers.id', 'suppliers.name', 'suppliers.mobile_no')
            ->selectRaw('SUM(supplier_purchese_payments.paid_amount) as total_paid')
            ->selectRaw('SUM(supplier_purchese_payments.due_amount) as total_due')
            ->join('supplier_purchese_payments', 'suppliers.id', '=', 'supplier_purchese_payments.supplier_id')
            // ->where('supplier_purchese_payments.due_amount', '!=', 0)
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.mobile_no')
            ->get();
        // dd($customer_due_and_payment_infos);
        return view('backend.purchese.supplier_wise_purchese_report', compact('supplier_purchase_and_payment_infos'));
    }
    // PDF
    public function PrintPurchase(Request $request, $id)
    {

        $purchase = SupplierPurchese::with('supplier_purchese_details')->findOrFail($id);
        $pre_due = SupplierPurchese::whereHas('supplier_purchese_payment', function ($q) use ($purchase) {
            $q->where('supplier_id', $purchase?->supplier_purchese_payment?->supplier_id);
        })->withSum('supplier_purchese_payment', 'due_amount')->where([['id', '!=', $id], ['status', 1]])->get()->sum('payment_sum_due_amount');
        return view('backend.purchese.pdf.purchase_pdf', compact('purchase', 'pre_due'));
    }
    // Purchase preview
    public function PreView(Request $request)
    {
        $data = $request->all();
        // dd($data);
        // Validate input
        $request->validate([
            'purchase_no' => 'required',
            'date' => 'required|date',
            'product_id' => 'required|array',
            'buying_price' => 'required|array',
            'estimated_amount' => 'required|numeric',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        // Validate paid amount
        if ($request->paid_amount > $request->total) {
            return redirect()->back()->with([
                'message' => 'Sorry, paid amount is greater than the total price.',
                'alert-type' => 'error'
            ]);
        }
        $finalProducts = [];

        foreach ($request->product_id as $index => $product_id) {
            $product = Product::find($product_id);

            if ($product) {
                $finalProducts[] = (object)[
                    'product_name'    => $product->name,
                    'brand'           => $product->brand->name ?? '',
                    'qty'             => $request->buying_qty[$index],
                    'unit'            => $product->unit->name,
                    'product_buying_price'      => $request->product_buying_price[$index],
                    'discount_rate'   => $request->discount_rate[$index] ?? 0,
                    'discount_amount' => $request->discount_amount_per_product[$index] ?? 0,
                    'total'           => $request->buying_price[$index],
                ];
            }
        }
        // dd($finalProducts);
        return view('backend.purchese.preview.purchase-preview', compact('data', 'finalProducts'));
    }
    public function SupplierAllTransaction(Request $request)
    {
        $suppliers_for_filter = Supplier::latest()->get();
        $show_start_date = $request->get('startDate');
        $show_end_date = $request->get('endDate');
        $filter = $request->get('filter');
        $supplier_filter = $request->get('supplier_filter');
        // Handle date range
        if ($show_start_date && $show_end_date) {
            $startDate = Carbon::parse($request->get('startDate'));
            $endDate = Carbon::parse($request->get('endDate'))->endOfDay();
        } else {
            $startDate = today()->subDays(30)->startOfDay();
            $endDate = today()->endOfDay();
        }
        // Get distinct invoice IDs from payment records
        $purchaseQuery = SupplierPurchesePayment::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($supplier_filter != null) {
            $purchaseQuery->where('supplier_id', $supplier_filter);
        }

        $purchaseIds = $purchaseQuery->pluck('purchase_id')->unique();
        // dd($purchaseIds);
        // Fetch and group payment details by created_at
        $paymentDetails = SupplierPurchesePaymentDetails::whereIn('purchase_id', $purchaseIds)->where('current_paid_amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->transaction_id;
            });
        // Prepare grouped report data
        $groupedDetails = [];
    
        foreach ($paymentDetails as $details) {
            $payment = $details[0];
            // assume first entry represents group
            $commonData = [
                'supplier_id' => $payment['supplier']->id,
                'supplier_name' => $payment['supplier']->name,
                'supplier_phone' => $payment['supplier']->mobile_no,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at,
                'total_amount' => $details->sum('current_paid_amount'),
                'purchase_ids' => implode(', ', $details->pluck('purchase_id')->toArray()),
                'payment_type' => $payment->payment_type == 'check_payment' ? 'Bank Payment' : ($payment->payment_type == 'mobile_banking' ? 'Mobile Banking' : ($payment->payment_type == 'online_transaction' ? 'Online Payment' : 'Cash Payment')),
                'status' => $payment->status == 'pending' ? 'Pending' : ($payment->status == 'paid' ? 'Paid' : ($payment->status == 'refunded' ? 'Refunded' : 'Rejected')),
                'payee_name' => $payment->payee_name,
                'received_by' => $payment->received_by,
                'remarks' => $payment->remarks,
            ];
    
            $paymentDetailsExtra = [];
    
            if ($payment->payment_type == 'check_payment') {
                $paymentDetailsExtra = [
                    'bank_name' => $payment->bank_name,
                    'bank_branch_name' => $payment->bank_branch_name,
                    'bank_account_number' => $payment->bank_account_number,
                    'bank_cheque_number' => $payment->bank_cheque_number,
                    'bank_micr_code' => $payment->bank_micr_code,
                    'bank_check_issue_date' => $payment->bank_check_issue_date,
                    'bank_check_cleared_at' => $payment->bank_check_cleared_at,
                    'bank_cheque_image' => $payment->bank_cheque_image,
                ];
            } elseif ($payment->payment_type == 'mobile_banking') {
                $paymentDetailsExtra = [
                    'mobile_banking_type' => $payment->mobile_banking_type,
                    'mobile_banking_account_type' => $payment->mobile_banking_account_type,
                    'mobile_banking_sender_number' => $payment->mobile_banking_sender_number,
                    'mobile_banking_receiver_number' => $payment->mobile_banking_receiver_number,
                    'mobile_banking_transaction_id' => $payment->mobile_banking_transaction_id,
                ];
            } elseif ($payment->payment_type == 'online_transaction') {
                $paymentDetailsExtra = [
                    'bank_name' => $payment->bank_name,
                    'bank_branch_name' => $payment->bank_branch_name,
                    'sender_account_number' => $payment->sender_account_number,
                    'receiver_account_number' => $payment->receiver_account_number,
                    'online_transaction_id' => $payment->online_transaction_id,
                    'online_transfer_method' => $payment->online_transfer_method,
                ];
            }
    
            $groupedDetails[] = array_merge($commonData, $paymentDetailsExtra);
        }
        // dd($groupedDetails);
        return view('backend.purchese.supplier_all_transactions', compact(
            'suppliers_for_filter', 
            'groupedDetails',
            'show_start_date',
            'show_end_date',
            'filter',
            'supplier_filter'));
    } // End Method

    public function SupplierTransactionEdit($transactionId)
    {
        $payments = SupplierPurchesePaymentDetails::with('supplier')->where('transaction_id', $transactionId)->get();

        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'No payments found.');
        }

        // Convert to array
        $allPayments = $payments->toArray();

        // Get common fields
        $common = [];
        foreach ($allPayments[0] as $key => $value) {
            $same = true;
            foreach ($allPayments as $payment) {
                if ($payment[$key] !== $value) {
                    $same = false;
                    break;
                }
            }
            if ($same) {
                $common[$key] = $value;
            }
        }

        // Get the differences
        $differences = [];
        foreach ($allPayments as $payment) {
            $diff = [];
            foreach ($payment as $key => $value) {
                if (!isset($common[$key])) {
                    $diff[$key] = $value;
                }
            }
            $differences[] = $diff;
        }
        // Collect invoice IDs
        $purchase_ids = array_column($allPayments, 'purchase_id');

        // You can use this as an array or join it into a string
        $purchase_ids_string = implode(', ', $purchase_ids);

        // Sum current_paid_amount
        $total_paid = array_sum(array_column($allPayments, 'current_paid_amount'));
        // dd($common);
        // dd($differences);
        return view('backend.purchese.supplier_transaction_edit', compact('payments', 'common', 'differences', 'transactionId', 'purchase_ids_string', 'total_paid'));
    } // End Method
    
    public function SupplierTransactionUpdate(Request $request)
    {
        // dd($request->all());
        // Validate the request
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|string',
            'date' => 'required|date',
            'status' => 'required|string',
            'payee_name' => 'nullable|string|max:255',
            'received_by' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',
        ]);

        // Find the payment details by transaction ID
        $paymentDetails = SupplierPurchesePaymentDetails::where('transaction_id', $request->transaction_id)->get();
        if ($paymentDetails->isEmpty()) {
            return redirect()->back()->with('error', 'No payments found for this transaction.');
        }

        // Update each payment detail
        foreach ($paymentDetails as $detail) {
            $detail->payment_type = $request->payment_type;
            $detail->status = $request->status;
            $detail->payee_name = $request->payee_name;
            $detail->received_by = $request->received_by;
            $detail->remarks = $request->remarks;
            if ($request->payment_type == 'check_payment') {
                    $detail->bank_name = $request->bank_name;
                    $detail->bank_branch_name = $request->bank_branch_name;
                    $detail->bank_account_number = $request->bank_account_number;
                    $detail->bank_cheque_number = $request->bank_cheque_number;
                    $detail->bank_micr_code = $request->bank_micr_code;
                    $detail->bank_check_issue_date = $request->bank_check_issue_date;
                    $detail->bank_check_cleared_at = $request->bank_check_cleared_at;
                    $detail->bank_cheque_image = $request->bank_cheque_image;
            } elseif ($request->payment_type == 'mobile_banking') {
                    $detail->mobile_banking_type = $request->mobile_banking_type;
                    $detail->mobile_banking_account_type = $request->mobile_banking_account_type;
                    $detail->mobile_banking_sender_number = $request->mobile_banking_sender_number;
                    $detail->mobile_banking_receiver_number = $request->mobile_banking_receiver_number;
                    $detail->mobile_banking_transaction_id = $request->mobile_banking_transaction_id;
            } elseif ($request->payment_type == 'online_transaction') {
                    $detail->bank_name = $request->bank_name;
                    $detail->bank_branch_name = $request->bank_branch_name;
                    $detail->sender_account_number = $request->sender_account_number;
                    $detail->receiver_account_number = $request->receiver_account_number;
                    $detail->online_transaction_id = $request->online_transaction_id;
                    $detail->online_transfer_method = $request->online_transfer_method;
            }
            // Save the updated payment detail
            $detail->save();
        }

        return redirect()->back()->with('success', 'Payment details updated successfully.');
    }
    public function SupplierAllTransactionPdf($show_start_date = null, $show_end_date = null, $filter = 'null',$supplier_filter = 'null')
    {
        $filter = $filter;
        $supplier_filter = $supplier_filter;
        // Handle date range
        if ($show_start_date && $show_end_date) {
            $startDate = Carbon::parse($show_start_date);
            $endDate = Carbon::parse($show_end_date)->endOfDay();
        } else {
            $startDate = today()->subDays(30)->startOfDay();
            $endDate = today()->endOfDay();
        }
        // Get distinct invoice IDs from payment records
        $purchaseQuery = SupplierPurchesePayment::query()
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($supplier_filter != 'null') {
            $purchaseQuery->where('supplier_id', $supplier_filter);
        }
        $purchaseIds = $purchaseQuery->pluck('purchase_id')->unique();
        // dd($invoiceIds);
        // Fetch and group payment details by created_at
        $paymentDetails = SupplierPurchesePaymentDetails::whereIn('purchase_id', $purchaseIds)->where('current_paid_amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->transaction_id;
            });
        // Prepare grouped report data
        $groupedDetails = [];
        foreach ($paymentDetails as $details) {
            $payment = $details[0]; // assume first entry represents group
            $commonData = [
                'supplier_name' => $payment['supplier']->name,
                'supplier_phone' => $payment['supplier']->mobile_no,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at,
                'total_amount' => $details->sum('current_paid_amount'),
                'purchase_ids' => implode(', ', $details->pluck('purchase_id')->toArray()),
                'payment_type' => $payment->payment_type == 'check_payment' ? 'Bank Payment' : ($payment->payment_type == 'mobile_banking' ? 'Mobile Banking' : ($payment->payment_type == 'online_transaction' ? 'Online Payment' : 'Cash Payment')),
                'status' => $payment->status == 'pending' ? 'Pending' : ($payment->status == 'paid' ? 'Paid' : ($payment->status == 'refunded' ? 'Refunded' : 'Rejected')),
                'payee_name' => $payment->payee_name,
                'received_by' => $payment->received_by,
                'remarks' => $payment->remarks,
            ];
    
            $paymentDetailsExtra = [];
    
            if ($payment->payment_type == 'check_payment') {
                $paymentDetailsExtra = [
                    'bank_name' => $payment->bank_name,
                    'bank_branch_name' => $payment->bank_branch_name,
                    'bank_account_number' => $payment->bank_account_number,
                    'bank_cheque_number' => $payment->bank_cheque_number,
                    'bank_micr_code' => $payment->bank_micr_code,
                    'bank_check_issue_date' => $payment->bank_check_issue_date,
                    'bank_check_cleared_at' => $payment->bank_check_cleared_at,
                    'bank_cheque_image' => $payment->bank_cheque_image,
                ];
            } elseif ($payment->payment_type == 'mobile_banking') {
                $paymentDetailsExtra = [
                    'mobile_banking_type' => $payment->mobile_banking_type,
                    'mobile_banking_account_type' => $payment->mobile_banking_account_type,
                    'mobile_banking_sender_number' => $payment->mobile_banking_sender_number,
                    'mobile_banking_receiver_number' => $payment->mobile_banking_receiver_number,
                    'mobile_banking_transaction_id' => $payment->mobile_banking_transaction_id,
                ];
            } elseif ($payment->payment_type == 'online_transaction') {
                $paymentDetailsExtra = [
                    'bank_name' => $payment->bank_name,
                    'bank_branch_name' => $payment->bank_branch_name,
                    'sender_account_number' => $payment->sender_account_number,
                    'receiver_account_number' => $payment->receiver_account_number,
                    'online_transaction_id' => $payment->online_transaction_id,
                    'online_transfer_method' => $payment->online_transfer_method,
                ];
            }
    
            $groupedDetails[] = array_merge($commonData, $paymentDetailsExtra);
        }
        // dd($groupedDetails);
        return view('backend.purchese.pdf.all_supplier_transactions_report', compact(
            'groupedDetails',
            'show_start_date',
            'show_end_date',
            'filter',
            'supplier_filter'));
    } // End Method
}
