<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\AdditionalFee;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Customer;
use App\Models\ProductPriceCodes;
use App\Models\Tax;
use Auth;
use Illuminate\support\Carbon;
use DB;
use App\Traits\MailAndSmsHelper;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    use MailAndSmsHelper;
    public function __construct()
    {
        $this->middleware(['permission:invoice-create'], ['only' => ['InvoiceAdd', 'InvoiceStore']]);
        $this->middleware(['permission:invoice-edit'], ['only' => ['InvoiceEdit', 'InvoiceUpdate']]);
        $this->middleware(['permission:invoice-delete'], ['only' => ['InvoiceEdit', 'InvoiceDelete']]);
    }
    // Invoice Show Data
    public function InvoiceAll(Request $request)
    {
        $all_customers = Customer::all();
        $show_start_date = $request->get('startDate');
        $show_end_date = $request->get('endDate');
        $filter = $request->get('filter');
        $invoice_type_filter = $request->get('invoice_type_filter');
        $customer_filter = $request->get('customer_filter');

        // Handle date range
        if ($request->get('startDate') && $request->get('endDate')) {
            $startDate = Carbon::parse($request->get('startDate'));
            $endDate = Carbon::parse($request->get('endDate'))->endOfDay();
        } else {
            $startDate = Carbon::parse(today())->subDays(30)->startOfDay();
            $endDate = Carbon::parse(today())->endOfDay();
        }

        // Build base query
        $allDataQuery = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '1')
            ->orderBy('created_at', 'desc');

        // Apply invoice type filter
        if ($invoice_type_filter != null) {
            if (in_array($invoice_type_filter, ['draft', 'challan', 'quotation', 'invoice'])) {
                $allDataQuery->where('invoice_type', $invoice_type_filter);
            }
        }

        // Apply customer filter
        if ($customer_filter != null) {
            $find_customer_invoices = Payment::where('customer_id', $customer_filter)->pluck('invoice_id')->toArray();
            if (!empty($find_customer_invoices)) {
                $allDataQuery->whereIn('id', $find_customer_invoices);
            } else {
                // If no invoice found for customer, return empty collection
                $allDataQuery->whereRaw('0 = 1');
            }
        }

        // Final query execution
        $allData = $allDataQuery->get();

        // Get filtered invoice IDs from the main invoice query
        $filtered_invoice_ids = $allData->whereNotIn('invoice_type', ['draft', 'quotation'])->pluck('id')->toArray();

        // Filter payments based on invoices
        $payment = Payment::whereIn('invoice_id', $filtered_invoice_ids)->get();

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

        // Filter invoice details based on filtered invoice IDs
        $invoice_details = InvoiceDetail::whereIn('invoice_id', $filtered_invoice_ids)->get();

        $total_profit = 0;
        $total_qty = 0;
        $total_selling_price = 0;
        $total_buying_price = 0;

        foreach ($invoice_details as $inv) {
            $total_selling_price += $inv->selling_price;
            $total_buying_price += $inv->buying_price;
            $total_qty += $inv->selling_qty;
        }

        $total_profit = $total_selling_price - $total_buying_price - $total_discount;


        return response()->view('backend.invoice.invoice_all', compact(
            'allData',
            'all_customers',
            'filter',
            'show_start_date',
            'show_end_date',
            'startDate',
            'endDate',
            'invoice_type_filter',
            'customer_filter',
            'total_amount',
            'total_profit',
            'total_paid',
            'total_due'
        ));
    }
    //End Method
    // Invoice add form
    public function InvoiceAdd()
    {
        $category = Category::all();
        $brands = Brand::all();
        $customer = Customer::all();
        $payment = Payment::all();
        $invoice_data = Invoice::orderBy('id', 'desc')->first();
        $products = Product::latest()->get();
        $tax = Tax::orderBy('id', 'desc')->get();
        $additional_fees = AdditionalFee::orderBy('id', 'desc')->get();
        if ($invoice_data == null) {
            $firstReg = '0';
            $invoice_no = $firstReg + 1;
        } else {
            $invoice_data = Invoice::orderBy('id', 'desc')->first()->id;
            $invoice_no = $invoice_data + 1;
        }
        $productPriceCode = ProductPriceCodes::all();
        $date = date('Y-m-d');
        return view('backend.invoice.invoice_add', compact('invoice_no', 'products', 'category', 'date', 'customer', 'payment', 'brands', 'productPriceCode', 'tax', 'additional_fees'));
    } //End Method
    public function InvoiceStore(Request $request)
    {
        // dd($request->saveBtn);
        // Validate input
        $request->validate([
            'invoice_no' => 'required',
            'date' => 'required|date',
            'product_id' => 'required|array',
            'selling_qty' => 'required|array',
            'unit_price' => 'required|array',
            'selling_price' => 'required|array',
            'estimated_amount' => 'required|numeric|min:1',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        // Check if any items are selected
        if ($request->total == 0) {
            return redirect()->back()->with([
                'message' => 'Sorry, you did not select any item.',
                'alert-type' => 'error'
            ]);
        }

        // Validate paid amount
        if ($request->paid_amount > $request->total) {
            return redirect()->back()->with([
                'message' => 'Sorry, paid amount is greater than the total price.',
                'alert-type' => 'error'
            ]);
        }

        // Transaction ensures atomicity
        $invoiceId = DB::transaction(function () use ($request) {
            // Create Invoice
            if ($request->saveBtn == 2) {
                $invoice_type = 'invoice';
            } else if ($request->saveBtn == 4) {
                $invoice_type = 'challan';
            } else if ($request->saveBtn == 1) {
                $invoice_type = 'quotation';
            } else {
                $invoice_type = 'draft';
            }
            $invoice = Invoice::create([
                'invoice_no' => $request->invoice_no,
                'dn_no' => $request->invoice_no,
                'wo_no' => $request->wo_no,
                'invoice_type' => $invoice_type,
                'date' => date('Y-m-d', strtotime($request->date)),
                'description' => $request->description,
                'invoice_tax_type' => json_encode($request->total_taxes ?? []),
                'invoice_tax_amount' => $request->invoice_tax_amount,
                'invoice_discount_type' => $request->discount_status,
                'invoice_discount_rate' => ($request->discount_status === 'fixed_discount' ? $request->discount_show : $request->discount_amount),
                'invoice_discount_amount' => ($request->discount_status === 'fixed_discount' ? $request->discount_amount : $request->discount_show),
                'additional_charge_type' => json_encode($request->total_additional_fees_type ?? []),
                'additional_charge_amount' => $request->total_additional_fees_amount,
                'status' => '1', // Change this to '0' if approval is required
                'created_by' => Auth::id(),
                'created_at' => Carbon::now(),
            ]);

            $total_selling_price = 0;

            // Insert Invoice Details & Update Product Stock
            $total_product = count($request->product_id);
            foreach ($request->product_id as $index => $productId) {
                $buying_price = $request->buying_price[$index];
                $selling_price = $request->selling_price[$index];
                $total_selling_price += $selling_price;
                InvoiceDetail::create([
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'invoice_id' => $invoice->id,
                    'category_id' => $request->category_id[$index],
                    'product_id' => $productId,
                    'selling_qty' => $request->selling_qty[$index],
                    'unit_price' => $request->unit_price[$index],
                    'buying_price' => $buying_price,
                    'selling_price' => $selling_price,
                    'profit' => $selling_price - $buying_price,
                    'discount_type' => $request->discount_rate[$index] == $request->discount_amount_per_product[$index] ? 'fixed' : 'percentage',
                    'discount_rate' => $request->discount_rate[$index],
                    'discount_amount' => $request->discount_amount_per_product[$index],
                    'tax_type' => json_encode($request->product_tax[$productId] ?? []),
                    'tax_amount' => $request->product_tax_amount[$index],
                    'status' => '1',
                    'created_at' => Carbon::now(),
                ]);

                // Update Product Stock, when it is invoice or challan
                if ($request->saveBtn == 2 || $request->saveBtn == 4) {
                    Product::where('id', $productId)->decrement('quantity', $request->selling_qty[$index]);
                }
            }

            // Handle New or Existing Customer
            $customer_id = ($request->customer_id == '0')
                ? Customer::create([
                    'name' => $request->name,
                    'mobile_no' => $request->mobile_no,
                    'email' => $request->email,
                    'address' => $request->address,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ])->id
                : $request->customer_id;

            // Create Payment Record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $customer_id,
                'paid_status' => $request->paid_status,
                'discount_amount' => $request->total_discount_amount,
                'total_amount' => $request->total,
                'paid_amount' => $request->paid_status == 'full-paid' ? $request->total : ($request->paid_status == 'partial-paid' ? $request->paid_amount : 0),
                'due_amount' => $request->paid_status == 'full-paid' ? 0 : $request->total - ($request->paid_status == 'partial-paid' ? $request->paid_amount : 0),
                'total_tax_amount' => $request->tax_value,
                'total_additional_charge_amount' => $request->total_additional_fees_amount,
            ]);

            // Create Payment Detail Record
            if ($request->saveBtn == 2 || $request->saveBtn == 4) {
                PaymentDetail::create([
                    'customer_id' => $customer_id,
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(4)) . '-' . Str::uuid(),
                    'invoice_id' => $invoice->id,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'current_paid_amount' => $payment->paid_amount,
                    'received_by' => Auth::user()->name
                ]);
            }
            return $invoice->id;  // Return Invoice ID
        });

        // Fetch invoice after transaction
        $invoice = Invoice::with('invoice_details')->findOrFail($invoiceId);

        // Handle Save Button Logic
        $redirect = 'invoice-add';
        switch ($request->saveBtn) {
            case 4:
                return view('backend.pdf.challan_pdf_print_by_add_invoice', compact('invoice', 'redirect'));
            case 3:
                return redirect()->route('invoice.add')->with([
                    'message' => 'Invoice Draft Saved Successfully',
                    'alert-type' => 'success'
                ]);
            case 2:
                $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
                    $q->where('customer_id', $invoice->payment->customer_id);
                })
                    ->where('id', '!=', $invoiceId)
                    ->where('status', 1)
                    ->withSum('payment', 'due_amount')
                    ->get()
                    ->sum('payment_sum_due_amount');

                return view('backend.pdf.invoice_pdf_print_by_add_invoice', compact('invoice', 'pre_due', 'redirect'));

            case 1:
                $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
                    $q->where('customer_id', $invoice->payment->customer_id);
                })
                    ->where('id', '!=', $invoiceId)
                    ->where('status', 1)
                    ->withSum('payment', 'due_amount')
                    ->get()
                    ->sum('payment_sum_due_amount');

                return view('backend.invoice.pdf.quotation_pdf', compact('invoice', 'pre_due', 'redirect'));
            case 0:
            default:
                return redirect()->route('invoice.add')->with([
                    'message' => 'Invoice Draft Saved Successfully',
                    'alert-type' => 'success'
                ]);
        }
    }

    public function InvoiceSmsSend(Request $request)
    {
        $this->validate($request, ['id' => 'required', 'sr_id' => 'required|integer|min:0']);
        if ($request->sr_id > 0) {
            $invoice = Invoice::with('payment.customer')->whereHas('payment.customer')->find($request->id);
        } else {
            $invoice = Invoice::with('sales_rep')->whereHas('sales_rep')->find($request->id);
        }
        if (!$invoice) {
            return response(['status' => false, 'message' => $request->sr_id > 0 ? 'SR not found.' : 'Customer not found.'], 403);
        }

        if ($request->sr_id > 0) {
            $number = $invoice?->sales_rep->mobile_no;
            $message = "Dear " . $invoice?->sales_rep?->name . " pls collect invoice for customer: " . $invoice?->payment?->customer?->name . "\nlink: " . route('PublicPrintInvoice', base64_encode($invoice->id)) . "\n-Foisal";
        } else {
            $number = $invoice?->payment?->customer?->mobile_no;
            $message = "Dear " . $invoice?->payment?->customer?->name . "\nPls collect your invoice.\nLink: " . route('PublicPrintInvoice', base64_encode($invoice->id)) . "\nThanks by Foisal";
        }

        if ($this->send_sms($number, $message)) {
            return response(['message' => "Message send successful."], 200);
        } else {
            return response(['status' => false, 'message' => 'Message not send.'], 403);
        }
    }
    // Invoice Edit
    public function InvoiceEdit($id)
    {
        $brands = Brand::all();
        $category = Category::all();
        $customers = Customer::all();
        $products = Product::latest()->get();
        $tax = Tax::orderBy('id', 'desc')->get();
        $product_price_code = ProductPriceCodes::all();
        $invoice = Invoice::findOrFail($id);
        $payment = Payment::with('customer')->where('invoice_id', $invoice->id)->first();
        return view('backend.invoice.invoice_edit', compact('brands', 'category', 'customers', 'products', 'tax', 'product_price_code', 'invoice', 'payment'));
    }
    public function InvoiceUpdate(Request $request)
    {
        // dd($request->all());
        // Validate input
        $request->validate([
            'invoice_no' => 'required',
            'date' => 'required|date',
            'product_id' => 'required|array',
            'invoice_details_id' => 'required|array',
            'selling_qty' => 'required|array',
            'unit_price' => 'required|array',
            'selling_price' => 'required|array',
            'estimated_amount' => 'required|numeric|min:1',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);
        // Check if any items are selected
        if ($request->total == 0) {
            return redirect()->back()->with([
                'message' => 'Sorry, you did not select any item.',
                'alert-type' => 'error'
            ]);
        }

        // Validate paid amount
        if ($request->paid_amount > $request->total) {
            return redirect()->back()->with([
                'message' => 'Sorry, paid amount is greater than the total price.',
                'alert-type' => 'error'
            ]);
        }

        DB::transaction(function () use ($request) {
            //update invoice
            $invoice = Invoice::findOrFail($request->invoice_no);
            $old_invoice_type = $invoice->invoice_type;

            // Determine new invoice type based on saveBtn
            switch ($request->saveBtn) {
                case 2:
                    $invoice_type = 'invoice';
                    break;
                case 4:
                    $invoice_type = 'challan';
                    break;
                case 1:
                    $invoice_type = 'quotation';
                    break;
                default:
                    $invoice_type = $old_invoice_type;
            }

            // Restrict downgrading to quotation if old type is invoice or challan
            if ($invoice_type === 'quotation' && in_array($old_invoice_type, ['invoice', 'challan'])) {
                $invoice_type = $old_invoice_type;
            }

            $invoice->update([
                'invoice_no' => $request->invoice_no,
                'dn_no' => $request->invoice_no,
                'wo_no' => $request->wo_no,
                'invoice_type' => $invoice_type,
                'date' => date('Y-m-d', strtotime($request->date)),
                'description' => $request->description,
                'invoice_tax_type' => json_encode($request->total_taxes ?? []),
                'invoice_tax_amount' => $request->invoice_tax_amount,
                'invoice_discount_type' => $request->discount_status,
                'invoice_discount_rate' => ($request->discount_status === 'fixed_discount' ? $request->discount_show : $request->discount_amount),
                'invoice_discount_amount' => ($request->discount_status === 'fixed_discount' ? $request->discount_amount : $request->discount_show),
                'additional_charge_type' => json_encode($request->total_additional_fees_type ?? []),
                'additional_charge_amount' => $request->total_additional_fees_amount,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            $existingDetails = InvoiceDetail::where('invoice_id', $request->invoice_no)->get()->keyBy('id');

            $submittedIds = collect($request->invoice_details_id)->filter(fn($id) => $id > 0)->values();

            // Handle deleted items
            foreach ($existingDetails as $detailId => $detail) {
                if (!$submittedIds->contains($detailId)) {
                    if (in_array($old_invoice_type, ['invoice', 'challan'])) {
                        Product::where('id', $detail->product_id)->increment('quantity', $detail->selling_qty); // restore stock
                    }
                    $detail->delete();
                }
            }

            $total_product = count($request->product_id);

            for ($index = 0; $index < $total_product; $index++) {
                $productId = $request->product_id[$index];
                $detailId = $request->invoice_details_id[$index];
                $sellingQty = $request->selling_qty[$index];
                $buyingPrice = $request->buying_price[$index];
                $sellingPrice = $request->selling_price[$index];

                $data = [
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'invoice_id' => $invoice->id,
                    'category_id' => $request->category_id[$index],
                    'product_id' => $productId,
                    'selling_qty' => $sellingQty,
                    'unit_price' => $request->unit_price[$index],
                    'buying_price' => $buyingPrice,
                    'selling_price' => $sellingPrice,
                    'profit' => $sellingPrice - $buyingPrice,
                    'discount_type' => $request->discount_rate[$index] == $request->discount_amount_per_product[$index] ? 'fixed' : 'percentage',
                    'discount_rate' => $request->discount_rate[$index],
                    'discount_amount' => $request->discount_amount_per_product[$index],
                    'tax_type' => json_encode($request->product_tax[$productId] ?? []),
                    'tax_amount' => $request->product_tax_amount[$index],
                    'status' => '1',
                    'updated_at' => Carbon::now(),
                ];

                if ($detailId == 0) {
                    InvoiceDetail::create($data);
                    if (
                        (in_array($invoice_type, ['invoice', 'challan']) && in_array($old_invoice_type, ['draft', 'quotation'])) ||
                        in_array($old_invoice_type, ['invoice', 'challan'])
                    ) {
                        Product::where('id', $productId)->decrement('quantity', $sellingQty);
                    }
                } else {
                    $existingDetail = $existingDetails[$detailId];
                    $oldQty = $existingDetail->selling_qty;
                    $qtyDiff = $sellingQty - $oldQty;

                    $existingDetail->update($data);
                    if (in_array($invoice_type, ['invoice', 'challan']) && in_array($old_invoice_type, ['draft', 'quotation'])) {
                        Product::where('id', $productId)->decrement('quantity', $sellingQty);
                    }
                    if (in_array($old_invoice_type, ['invoice', 'challan'])) {
                        Product::where('id', $productId)->decrement('quantity', $qtyDiff);
                    }
                }
            }

            $payment = Payment::where('invoice_id', $invoice->id)->first();

            $customer_id = $request->customer_id;

            if ($customer_id == 0) {
                $customer_id = Customer::create([
                    'name' => $request->name,
                    'mobile_no' => $request->mobile_no,
                    'email' => $request->email,
                    'address' => $request->address,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ])->id;
            }
            $payment->update([
                'customer_id' => $customer_id,
                'paid_status' => $request->paid_status,
                'discount_amount' => $request->total_discount_amount,
                'total_amount' => $request->total,
                'paid_amount' => $request->paid_status == 'full-paid' ? $request->total : ($request->paid_status == 'partial-paid' ? $request->paid_amount : 0),
                'due_amount' => $request->paid_status == 'full-paid' ? 0 : $request->total - ($request->paid_status == 'partial-paid' ? $request->paid_amount : 0),
                'total_tax_amount' => $request->tax_value,
                'total_additional_charge_amount' => $request->total_additional_fees_amount,
            ]);

            if (in_array($old_invoice_type, ['draft', 'quotation']) && in_array($invoice_type, ['invoice', 'challan'])) {
                PaymentDetail::create([
                    'customer_id' => $customer_id,
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(3)) . '-' . Str::uuid(),
                    'invoice_id' => $invoice->id,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'current_paid_amount' => $payment->paid_amount,
                    'received_by' => Auth::user()->name
                ]);
            } elseif (in_array($old_invoice_type, ['invoice', 'challan'])) {
                $payment_details = PaymentDetail::where('invoice_id', $invoice->id)->first();

                $payment_details->update(
                    [
                        'customer_id' => $customer_id,
                        'date' => date('Y-m-d', strtotime($request->date)),
                        'current_paid_amount' => $payment->paid_amount,
                    ]
                );
            }
            // dd('ok');
        });
        // Handle Save Button Logic
        $invoice = Invoice::findOrFail($request->invoice_no);
        $redirect = 'invoice-edit';
        switch ($request->saveBtn) {
            case 4:
                return view('backend.pdf.challan_pdf_print_by_add_invoice', compact('invoice', 'redirect'));
            case 3:
                return redirect()->route('invoice.add')->with([
                    'message' => 'Invoice Draft Saved Successfully',
                    'alert-type' => 'success'
                ]);
            case 2:
                $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
                    $q->where('customer_id', $invoice->payment->customer_id);
                })
                    ->where('id', '!=', $invoice->id)
                    ->where('status', 1)
                    ->withSum('payment', 'due_amount')
                    ->get()
                    ->sum('payment_sum_due_amount');

                return view('backend.pdf.invoice_pdf_print_by_add_invoice', compact('invoice', 'pre_due', 'redirect'));
            case 1:
                $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
                    $q->where('customer_id', $invoice->payment->customer_id);
                })
                    ->where('id', '!=', $invoice->id)
                    ->where('status', 1)
                    ->withSum('payment', 'due_amount')
                    ->get()
                    ->sum('payment_sum_due_amount');

                return view('backend.invoice.pdf.quotation_pdf', compact('invoice', 'pre_due', 'redirect'));
            case 0:
            default:
                return redirect()->route('invoice.edit', $request->invoice_no)->with([
                    'message' => 'Invoice Updated Successfully.',
                    'alert-type' => 'success'
                ]);
        }
    }
    // Invoice Delete 
    public function InvoiceDelete($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        InvoiceDetail::where('invoice_id', $invoice->id)->delete();
        Payment::where('invoice_id', $invoice->id)->delete();
        PaymentDetail::where('invoice_id', $invoice->id)->delete();

        $notification = array(
            'message' => 'Invoice Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } //End Method

    public function ReportPrint($id, $invoice_type)
    {
        // Fetch invoice after transaction
        $invoice = Invoice::with('invoice_details')->findOrFail($id);

        // Handle Save Button Logic
        $redirect = 'invoice-all';
        switch ($invoice_type) {
            case 3:
                $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
                    $q->where('customer_id', $invoice->payment->customer_id);
                })
                    ->where('id', '!=', $id)
                    ->where('status', 1)
                    ->withSum('payment', 'due_amount')
                    ->get()
                    ->sum('payment_sum_due_amount');

                return view('backend.invoice.pdf.quotation_pdf', compact('invoice', 'pre_due', 'redirect'));
            case 2:
                $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
                    $q->where('customer_id', $invoice->payment->customer_id);
                })
                    ->where('id', '!=', $id)
                    ->where('status', 1)
                    ->withSum('payment', 'due_amount')
                    ->get()
                    ->sum('payment_sum_due_amount');

                return view('backend.pdf.invoice_pdf_print_by_add_invoice', compact('invoice', 'pre_due', 'redirect'));
            case 1:
                return view('backend.pdf.challan_pdf_print_by_add_invoice', compact('invoice', 'redirect'));
        }
    }
    public function PrintInvoiceList()
    {
        $allData = Invoice::orderBy('date', 'desc')->orderBy('id', 'desc')->where('status', '1')->get();
        return view('backend.invoice.print_invoice_list', compact('allData'));
    } // End Method

    // Direct Invoice print from Add invoice page
    public function InvoicePosPrint($id)
    {
        // dd($id);
        $invoice = Invoice::with('invoice_details')->findOrFail($id);
        $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
            $q->where('customer_id', $invoice?->payment?->customer_id);
        })->withSum('payment', 'due_amount')->where([['id', '!=', $id], ['status', 1]])->get()->sum('payment_sum_due_amount');
        // dd($invoice);
        return view('backend.pdf.invoice_pos_print', compact('invoice', 'pre_due'));
    } // End Method
    public function PrintInvoice(Request $request, $id)
    {
        // dd($request->route()->getName());
        // if ($request->route()->getName() == "PublicPrintInvoice") {
        //     $invoice = Invoice::with('invoice_details')->findOrFail(base64_decode($id));
        //     $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
        //         $q->where('customer_id', $invoice?->payment?->customer_id);
        //     })->withSum('payment', 'due_amount')->where([['id', '!=', base64_decode($id)], ['status', 1]])->get()->sum('payment_sum_due_amount');
        //     return view('backend.pdf.public_invoice_pdf', compact('invoice', 'pre_due'));
        // }
        $invoice = Invoice::with('invoice_details')->findOrFail($id);
        $pre_due = Invoice::whereHas('payment', function ($q) use ($invoice) {
            $q->where('customer_id', $invoice?->payment?->customer_id);
        })->withSum('payment', 'due_amount')->where([['id', '!=', $id], ['status', 1]])->get()->sum('payment_sum_due_amount');
        return view('backend.pdf.invoice_pdf', compact('invoice', 'pre_due'));
    } // End Method

    public function DeliveryZoneInvoiceDetails(Request $request)
    {
        // $s_date = $request->start_date ?? date('Y-m-d', strtotime('-1 week'));
        $s_date = $request->start_date ?? date('Y-m-d');
        $e_date = $request->end_date ?? date('Y-m-d');
        $search_data = Invoice::whereHas('invoice_details', function ($q) {
            $q->where('status', 0);
        })->whereHas('sales_rep')->with(['delivery_zones', 'payment.customer', 'invoice_details.product', 'sales_rep'])->where('status', '0')->whereBetween('date', [$s_date, $e_date]);

        if (!empty($request->delivery_zone) && is_array($request->delivery_zone)) {
            $search_data = $search_data->whereIn('delivery_zone_id', array_values($request->delivery_zone));
        }
        if (!empty($request->customers) && is_array($request->customers)) {

            $search_data = $search_data->whereHas('payment', function ($q) use ($request) {
                $q->whereIn('customer_id', array_values($request->customers));
            });
        }
        if (!empty($request->products) && is_array($request->products)) {
            $search_data = $search_data->whereHas('invoice_details', function ($q) use ($request) {
                $q->whereIn('product_id', array_values($request->products));
            });
        }
        if (!empty($request->sr) && is_array($request->sr)) {
            $search_data = $search_data->whereIn('sales_rep_id', array_values($request->sr));
        }
        $search_data = $search_data->get()->groupBy('delivery_zone_id');
        return view('backend.invoice.deliveryzone_invoice_details', compact('search_data', 's_date', 'e_date'));
    } // End Method

    public function DeliveryZoneInvoicePdf(Request $request)
    {
        $sdate = date('Y-m-d', strtotime($request->start_date));
        $edate = date('Y-m-d', strtotime($request->end_date));

        // $invoice = Invoice::where('status','1')->get();
        // $allData  = InvoiceDetail::whereBetween('date',[$sdate,$edate])->where('invoice_id','$invoice->id')->get();
        // $allData  = InvoiceDetail::where('invoice_id','$invoice->id')->get();
        $allData = Invoice::whereBetween('date', [$sdate, $edate])->where('status', '1')->get();
        // $product = Product::where('id',$invoice_details->product_id)->first();
        // $allData  = InvoiceDetail::where('invoice_id',$invoice_id->id)->first();


        // $allData = InvoiceDetail::whereBetween('date',[$sdate,$edate])->get();
        $start_date = date('Y-m-d', strtotime($request->start_date));
        $end_date = date('Y-m-d', strtotime($request->end_date));
        return view('backend.pdf.deliveryzone_report_pdf', compact('allData', 'start_date', 'end_date'));
    } // End Method

    // Delivary zone Wise Summary
    public function DeliveryZoneInvoiceSummary(Request $request)
    {
        $s_date = $request->start_date ?? date('Y-m-d');
        $e_date = $request->end_date ?? date('Y-m-d');
        $search_data = Invoice::whereHas('invoice_details', function ($q) {
            $q->where('status', 0);
        })->whereHas('sales_rep')->with(['delivery_zones', 'payment.customer', 'invoice_details.product', 'sales_rep'])->where('status', '0')->whereBetween('date', [$s_date, $e_date]);

        if (!empty($request->delivery_zone) && is_array($request->delivery_zone)) {
            $search_data = $search_data->whereIn('delivery_zone_id', array_values($request->delivery_zone));
        }
        if (!empty($request->customers) && is_array($request->customers)) {

            $search_data = $search_data->whereHas('payment', function ($q) use ($request) {
                $q->whereIn('customer_id', array_values($request->customers));
            });
        }
        if (!empty($request->products) && is_array($request->products)) {
            $search_data = $search_data->whereHas('invoice_details', function ($q) use ($request) {
                $q->whereIn('product_id', array_values($request->products));
            });
        }
        if (!empty($request->sr) && is_array($request->sr)) {
            $search_data = $search_data->whereIn('sales_rep_id', array_values($request->sr));
        }
        $search_data = $search_data->get()->groupBy('delivery_zone_id');
        return view('backend.invoice.deliveryzone_invoice_summary', compact('search_data', 's_date', 'e_date'));
    } // End Method

    // Delivary zone Wise Summary Edit
    public function DeliveryZoneInvoiceEdit(Request $request)
    {
        $s_date = $request->start_date ?? date('Y-m-d');
        $e_date = $request->end_date ?? date('Y-m-d');
        $search_data = Invoice::whereHas('invoice_details', function ($q) {
            $q->where('status', 0);
        })->whereHas('sales_rep')->with(['delivery_zones', 'payment.customer', 'invoice_details.product', 'sales_rep'])->where('status', '0')->whereBetween('date', [$s_date, $e_date]);

        if (!empty($request->delivery_zone) && is_array($request->delivery_zone)) {
            $search_data = $search_data->whereIn('delivery_zone_id', array_values($request->delivery_zone));
        }
        if (!empty($request->customers) && is_array($request->customers)) {

            $search_data = $search_data->whereHas('payment', function ($q) use ($request) {
                $q->whereIn('customer_id', array_values($request->customers));
            });
        }
        if (!empty($request->products) && is_array($request->products)) {
            $search_data = $search_data->whereHas('invoice_details', function ($q) use ($request) {
                $q->whereIn('product_id', array_values($request->products));
            });
        }
        if (!empty($request->sr) && is_array($request->sr)) {
            $search_data = $search_data->whereIn('sales_rep_id', array_values($request->sr));
        }
        $search_data = $search_data->get()->groupBy('delivery_zone_id');
        return view('backend.invoice.deliveryzone_invoice_summary_edit', compact('search_data', 's_date', 'e_date'));
    } // End Method

    // Invoice All Page Print filter wise invoice/challan
    public function InvoiceAllFilterPrint($startDate = null, $endDate = null, $filter = 'null', $invoice_type_filter = 'null', $customer_filter = 'null')
    {
        $show_start_date = $startDate;
        $show_end_date = $endDate;

        // Handle date range
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subDays(30)->startOfDay();
        $endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay();

        // Build base query
        $allDataQuery = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '1')
            ->orderByDesc('created_at');

        // Apply invoice type filter
        if ($invoice_type_filter !== 'null') {
            $allDataQuery->where('invoice_type', in_array($invoice_type_filter, ['draft', 'challan']) ? $invoice_type_filter : 'invoice');
        }

        // Apply customer filter
        if ($customer_filter !== 'null') {
            $invoiceIds = Payment::where('customer_id', $customer_filter)->pluck('invoice_id');

            if ($invoiceIds->isNotEmpty()) {
                $allDataQuery->whereIn('id', $invoiceIds);
            } else {
                // No matching invoices, return empty result
                $allDataQuery->whereRaw('0 = 1');
            }
        }

        $allData = $allDataQuery->get();

        // Get filtered invoice IDs
        $filteredInvoiceIds = $allData->pluck('id');

        // Payment details
        $payments = Payment::whereIn('invoice_id', $filteredInvoiceIds)->get();

        $total_amount = $payments->sum('total_amount');
        $total_discount = $payments->sum('discount_amount');
        $total_due = $payments->sum('due_amount');
        $total_paid = $payments->sum('paid_amount'); // You were calculating it but not returning/using it

        // Invoice details
        $invoiceDetails = InvoiceDetail::whereIn('invoice_id', $filteredInvoiceIds)->get();

        $total_selling_price = $invoiceDetails->sum('selling_price');
        $total_buying_price = $invoiceDetails->sum('buying_price');
        $total_qty = $invoiceDetails->sum('selling_qty');

        $total_profit = $total_selling_price - $total_buying_price - $total_discount;

        return response()->view('backend.pdf.filter_invoice_print_from_invoice_all', compact(
            'allData',
            'filter',
            'show_start_date',
            'show_end_date',
            'startDate',
            'endDate',
            'invoice_type_filter',
            'customer_filter',
            'total_amount',
            'total_due'
        ));
    }
    // invoice preview
    public function PreView(Request $request)
    {
        $data = $request->all();
        if (collect($request->selling_qty)->sum() == 0 && $request->estimated_amount == 0) {
            return redirect()->back()->with([
                'message' => 'Sorry, you did not select any item.',
                'alert-type' => 'error'
            ]);
        }
        // dd($data);
        // Validate input
        $request->validate([
            'invoice_no' => 'required',
            'date' => 'required|date',
            'product_id' => 'required|array',
            'selling_qty' => 'required|array',
            'unit_price' => 'required|array',
            'selling_price' => 'required|array',
            'estimated_amount' => 'required|numeric|min:1',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        // Check if any items are selected
        if ($request->total == 0) {
            return redirect()->back()->with([
                'message' => 'Sorry, you did not select any item.',
                'alert-type' => 'error'
            ]);
        }

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
                    'product_description' => $product->description,
                    'brand'           => $product->brand->name ?? '',
                    'qty'             => $request->selling_qty[$index],
                    'unit'            => $product->unit->name,
                    'unit_price'      => $request->unit_price[$index],
                    'discount_rate'   => $request->discount_rate[$index] ?? 0,
                    'discount_amount' => $request->discount_amount_per_product[$index] ?? 0,
                    'total'           => $request->selling_price[$index],
                ];
            }
        }
        // dd($finalProducts);
        // Handle Save Button Logic
        if ($request->saveBtn == 4) {
            return view('backend.invoice.preview.challan-preview', compact('data', 'finalProducts'));
        } else if ($request->saveBtn == 2) {
            return view('backend.invoice.preview.invoice-preview', compact('data', 'finalProducts'));
        } else if ($request->saveBtn == 1) {
            return view('backend.invoice.preview.quotation-preview', compact('data', 'finalProducts'));
        } else {
            return view('backend.invoice.preview.invoice-preview', compact('data', 'finalProducts'));
        }
    }
    // invoice preview
    public function InvoiceAllPreView(Request $request)
    {
        $dataObject = FacadesDB::table('invoices')
            ->join('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->where('invoices.id', $request->invoice_id)
            ->select(
                'invoices.*',
                'invoices.invoice_discount_type as discount_status',
                'invoices.invoice_tax_type as total_taxes',
                'invoices.invoice_discount_amount as discount_show',
                'invoices.invoice_discount_amount as discount_amount',
                'payments.customer_id',
                'payments.paid_amount',
                'payments.due_amount',
                FacadesDB::raw('(payments.total_amount - payments.total_tax_amount - payments.total_additional_charge_amount) as estimated_amount'),
                'payments.discount_amount as total_discount_amount',
                'payments.total_tax_amount as tax_value',
                'payments.total_additional_charge_amount as total_additional_fees_amount'
            )
            ->first();

        $data = (array) $dataObject;
        // dd($data);
        $finalProducts = FacadesDB::table('invoice_details')
            ->join('products', 'invoice_details.product_id', '=', 'products.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->select(
                'invoice_details.id as invoice_detail_id',
                'invoice_details.invoice_id',
                'invoice_details.category_id',
                'invoice_details.product_id',
                'invoice_details.date',
                'invoice_details.selling_qty as qty',
                'invoice_details.unit_price',
                'invoice_details.discount_type',
                'invoice_details.discount_rate',
                'invoice_details.discount_amount',
                'invoice_details.tax_type',
                'invoice_details.tax_rate',
                'invoice_details.tax_amount',
                'invoice_details.profit',
                'invoice_details.buying_price',
                'invoice_details.selling_price as total',
                'invoice_details.total_sell_commission',
                'invoice_details.sell_commission',
                'invoice_details.status as invoice_status',

                // Product table columns
                'products.name as product_name',
                'products.sku',
                'products.product_code',
                'products.description as product_description',
                'products.product_buying_price',
                'products.product_offer_price',
                'products.product_price',
                'products.tax',
                'products.product_discount',
                'products.product_image',

                // Brand name from brands table
                'brands.name as brand',
                //Unit name from units table
                'units.name as unit'
            )
            ->where('invoice_details.invoice_id', $request->invoice_id)
            ->get();
        // dd($finalProducts);
        if ($request->invoice_type == 'challan') {
            return view('backend.invoice.preview.challan-preview', compact('data', 'finalProducts'));
        } else if ($request->invoice_type == 'invoice') {
            return view('backend.invoice.preview.invoice-preview', compact('data', 'finalProducts'));
        } else if ($request->invoice_type == 'quotation') {
            return view('backend.invoice.preview.quotation-preview', compact('data', 'finalProducts'));
        } else {
            return view('backend.invoice.preview.invoice-preview', compact('data', 'finalProducts'));
        }
    }
}
