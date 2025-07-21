<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $show_start_date = $request->get('startDate');
        $show_end_date = $request->get('endDate');
        $filter = $request->get('filter');
        if ($request->get('startDate') && $request->get('endDate')) {
            $startDate = Carbon::parse($request->get('startDate'));
            $endDate = Carbon::parse($request->get('endDate'))->endOfDay();
        } else {
            $startDate = Carbon::parse(today())->subDays(30)->startOfDay();
            $endDate = Carbon::parse(today())->endOfDay();
        }
        //
        $allData = Invoice::whereBetween('created_at', [$startDate, $endDate])->whereNotIn('invoice_type', ['draft', 'quotation'])->where('status', '1')->orderBy('created_at', 'desc')->get();
        // Get filtered invoice IDs from the main invoice query
        $filtered_invoice_ids = $allData->pluck('id')->toArray();
        // Fetch sales data based on the date range
        $payment = Payment::whereIn('invoice_id', $filtered_invoice_ids)->get();
        // dd( $payment);

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

        $invoice_details = InvoiceDetail::whereIn('invoice_id', $filtered_invoice_ids)->get();
        // dd($invoice_details->all());

        // Calculate total profit, total selling price, total buying price, and total quantity
        // Initialize totals
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


        // top selling products
        $top_selling_products = InvoiceDetail::select('product_id')
            ->selectRaw('SUM(selling_qty) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
        $low_stock_products = Product::where('quantity', '<=', 100)
            ->orderBy('quantity', 'asc')
            ->take(5)
            ->get();
        $out_of_stock_products = Product::where('quantity', '==', 0)
            ->take(5)
            ->get();
        return response()->view('admin.index', compact('allData', 'filter', 'show_start_date', 'show_end_date', 'startDate', 'endDate', 'total_amount', 'total_profit', 'total_paid', 'total_due', 'top_selling_products', 'low_stock_products', 'out_of_stock_products'));
    }

    public function dashboardReportPrint($startDate, $endDate, $filterName = 'Today', $total_amount, $total_profit, $total_paid, $total_due)
    {
        // dd($filterName);
        $allData = Invoice::whereBetween('created_at', [$startDate, $endDate])->orderBy('created_at', 'desc')->get();
        return view('backend.pdf.dashboardReportPrint', compact('allData', 'filterName', 'startDate', 'endDate', 'total_amount', 'total_profit', 'total_paid', 'total_due'));
    }
}
