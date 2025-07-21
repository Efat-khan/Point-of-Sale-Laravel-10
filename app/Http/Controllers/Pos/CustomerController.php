<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentDetail;
use Auth;
use Illuminate\support\Carbon;
use Image;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:customer-list'], ['only' => ['CustomerAll']]);
        $this->middleware(['permission:customer-create'], ['only' => ['CustomerAdd', 'CustomerStore']]);
        $this->middleware(['permission:customer-edit'], ['only' => ['CustomerEdit', 'CustomerUpdate']]);
        $this->middleware(['permission:customer-delete'], ['only' => ['CustomerDelete']]);
        $this->middleware(['permission:customer-transaction-list'], ['only' => ['CustomerDuePayment', 'CustomerAllTransaction']]);
        $this->middleware(['permission:customer-transaction-create'], ['only' => ['getInvoicesByCustomer', 'CustomerDueMakePayment']]);
        $this->middleware(['permission:customer-transaction-edit'], ['only' => ['CustomerDueMakePaymentEdit','CustomerDueMakePaymentUpdate']]);
    }
    // customer show from database
    public function CustomerAll(Request $request)
    {
        $customers_for_filter = Customer::latest()->get();
        $customer_id = $request->get('customer_filter');
        // dd($customer_id);
        if ($customer_id) {
            $customers = Customer::where('id', $customer_id)->latest()->get();
        } else {
            $customers = Customer::latest()->get();
        }
        return view('backend.customer.customer_all', compact('customers', 'customer_id', 'customers_for_filter'));
    } //End Method

    // customer Add form
    public function CustomerAdd()
    {
        return view('backend.customer.customer_add');
    } //End Method

    // Customer save from customer add form
    public function CustomerStore(Request $request)
    {
        if ($request->file('customer_image')) {
            $image = $request->file('customer_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension(); // Ex. 34343.jpg 
            Image::make($image)->resize(200, 200)->save('upload/customer_images/' . $name_gen);
            $save_url = 'upload/customer_images/' . $name_gen;

            Customer::insert([
                'name' => $request->name,
                'mobile_no' => $request->mobile_no,
                'alt_mobile_no' => $request->alt_mobile_no,
                'email' => $request->email,
                'alt_email' => $request->alt_email,
                'office_address' => $request->office_address,
                'factory_address' => $request->factory_address,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'customer_image' => $save_url,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        } else {
            Customer::insert([
                'name' => $request->name,
                'mobile_no' => $request->mobile_no,
                'alt_mobile_no' => $request->alt_mobile_no,
                'email' => $request->email,
                'alt_email' => $request->alt_email,
                'office_address' => $request->office_address,
                'factory_address' => $request->factory_address,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'customer_image' => 'upload/no_image.jpg',
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
            ]);
        }

        $notification = array(
            'message' => 'Customer Inserted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('customer.all')->with($notification);
    } //End Method

    public function CustomerEdit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('backend.customer.customer_edit', compact('customer'));
    } //End Method

    // Customer edited data save to database
    public function CustomerUpdate(Request $request)
    {
        $customer_id = $request->id;

        if ($request->file('customer_image')) {
            $image = $request->file('customer_image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension(); // Ex. 34343.jpg 
            Image::make($image)->resize(200, 200)->save('upload/customer_images/' . $name_gen);
            $save_url = 'upload/customer_images/' . $name_gen;

            Customer::findOrFail($customer_id)->update([
                'name' => $request->name,
                'customer_image' => $save_url,
                'mobile_no' => $request->mobile_no,
                'alt_mobile_no' => $request->alt_mobile_no,
                'email' => $request->email,
                'alt_email' => $request->alt_email,
                'office_address' => $request->office_address,
                'factory_address' => $request->factory_address,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Customer Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('customer.all')->with($notification);
        } else {
            Customer::findOrFail($customer_id)->update([
                'name' => $request->name,
                'mobile_no' => $request->mobile_no,
                'alt_mobile_no' => $request->alt_mobile_no,
                'email' => $request->email,
                'alt_email' => $request->alt_email,
                'office_address' => $request->office_address,
                'factory_address' => $request->factory_address,
                'contact_person_name' => $request->contact_person_name,
                'contact_person_phone' => $request->contact_person_phone,
                'updated_by' => Auth::user()->id,
                'updated_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Customer Updated Successfully without image',
                'alert-type' => 'success'
            );
            return redirect()->route('customer.all')->with($notification);
        } // End of Else

    } //End Method

    // Customer Delete form
    public function CustomerDelete($id)
    {
        $customers = Customer::findOrFail($id);
        $img = $customers->customer_image;

        if ($img != 'upload/no_image.jpg') {
            unlink($img);
        }
        Customer::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Customer Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } //End Method

    // Custoemr credit due amount report
    public function CreditCustomer()
    {
        $allData = Payment::whereIn('paid_status', ['partial-paid', 'full-due'])->get();
        // dd($allData);
        return view('backend.customer.customer_credit', compact('allData'));
    } //End Method


    // Customer Due amount PDF creation
    public function CreditCustomerPrintPdf()
    {
        $allData = Payment::whereIn('paid_status', ['partial-paid', 'full-due'])->get();
        return view('backend.pdf.customer_credit_pdf', compact('allData'));
    } // End Method


    public function CustomerEditInvoice($invoice_id)
    {
        $payment = Payment::where('invoice_id', $invoice_id)->first();
        return view('backend.customer.edit_customer_invoice', compact('payment'));
    } // End Method


    // Customer Due amount updated to Database 
    public function CustomerUpdateInvoice(Request $request, $invoice_id)
    {
        if ($request->new_paid_amount < $request->paid_amount) {
            $notification = array(
                'message' => 'Paid amount is more than due amount!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        } else {
            $payment = Payment::where('invoice_id', $invoice_id)->first();
            $payment_details = new PaymentDetail();
            $payment->paid_status = $request->paid_status;

            if ($request->paid_status == 'full-paid') {
                $payment->paid_amount = Payment::where('invoice_id', $invoice_id)->first()['paid_amount'] + $request->new_paid_amount;
                $payment->due_amount = '0';
                $payment_details->current_paid_amount = $request->new_paid_amount;
            } elseif ($request->paid_status == 'partial-paid') {
                $payment->paid_amount = Payment::where('invoice_id', $invoice_id)->first()['paid_amount'] + $request->paid_amount;
                $payment->due_amount = Payment::where('invoice_id', $invoice_id)->first()['due_amount'] - $request->paid_amount;
                $payment_details->current_paid_amount = $request->paid_amount;
            }
            $payment->save();
            $payment_details->invoice_id = $invoice_id;
            $payment_details->date = date('Y-m-d', strtotime($request->date));
            $payment_details->updated_by = Auth::user()->id;
            $payment_details->save();


            $notification = array(
                'message' => 'Invoice Updated successfully!',
                'alert-type' => 'success'
            );
            return redirect()->route('credit.customer')->with($notification);
        }
    } // End Method


    // Customer Details report
    public function CustomerInvoiceDetails($invoice_id)
    {
        $payment = Payment::where('invoice_id', $invoice_id)->first();
        return view('backend.pdf.invoice_details_pdf', compact('payment'));
    } // End Method


    // Customer Paid who has paid
    public function PaidCustomer()
    {
        $allData = Payment::where('paid_status', '!=', 'full-due')->get();
        return view('backend.customer.customer_paid', compact('allData'));
    } // End Method

    // Customer Paid Genenerating PDF
    public function PaidCustomerPrintPdf()
    {
        $allData = Payment::where('paid_status', '!=', 'full-due')->get();
        return view('backend.pdf.customer_paid_pdf', compact('allData'));
    } // End Method




    // Customer wise paid report
    public function CustomerWisePaidReport(Request $request)
    {
        $allData = Payment::where('customer_id', $request->customer_id)->where('paid_status', '!=', 'full-due')->get();
        return view('backend.pdf.customer_wise_paid_report', compact('allData'));
    } // End Method  

    // Customer wise due payment
    public function CustomerDuePayment()
    {
        $customer_due_and_payment_infos = Customer::select('customers.id', 'customers.name', 'customers.mobile_no')
            ->selectRaw('SUM(payments.paid_amount) as total_paid')
            ->selectRaw('SUM(payments.due_amount) as total_due')
            ->selectRaw('SUM(payments.total_amount) as total_amount')
            ->join('payments', 'customers.id', '=', 'payments.customer_id')
            ->where('customers.id', '!=', -1)
            ->where('payments.due_amount', '!=', 0)
            ->groupBy('customers.id', 'customers.name', 'customers.mobile_no')
            ->get();
        // dd($customer_due_and_payment_infos);
        return view('backend.customer.customer_due_payment', compact('customer_due_and_payment_infos'));
    }

    public function CustomerDueMakePayment(Request $request)
    {
        // dd($request->all());
        $new_paid_amount = $request->paid_amount;
        $transaction_id = 'TXN-' . strtoupper(Str::random(4)) . '-' . Str::uuid();

        if ($request->invoice_type === 'all') {
            if ($request->due_amount < $new_paid_amount) {
                $notification = array(
                    'message' => 'Paid amount is more than due amount!',
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
            $customer_wise_due = Payment::where('customer_id', $request->customer_id)
                ->where('paid_status', '!=', 'full-paid')
                ->orderBy('due_amount', 'desc')->get();
            //dd($customer_wise_due);
            foreach ($customer_wise_due as $due) {
                if ($new_paid_amount != 0) {

                    $payment = Payment::where('invoice_id', $due->invoice_id)->first();
                    $payment_details = new PaymentDetail();
                    if ($new_paid_amount >= $due->due_amount) {
                        $payment->paid_status = 'full-paid';
                        $payment->paid_amount += $due->due_amount;
                        $payment->due_amount = 0;

                        // payment details
                        $payment_details->transaction_id = $transaction_id;
                        $payment_details->current_paid_amount = $due->due_amount;
                        $payment_details->invoice_id = $due->invoice_id;

                        $payment_details->customer_id = $request->customer_id;
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

                        $payment->save();
                        $payment_details->save();

                        $new_paid_amount -= $due->due_amount;
                    } elseif ($new_paid_amount < $due->due_amount) {

                        $payment->paid_status = 'partial-paid';
                        $payment->paid_amount += $new_paid_amount;
                        $payment->due_amount -= $new_paid_amount;
                        $payment->save();

                        //payment details
                        $payment_details->transaction_id = $transaction_id;
                        $payment_details->current_paid_amount = $new_paid_amount;
                        $payment_details->invoice_id = $due->invoice_id;

                        $payment_details->customer_id = $request->customer_id;
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
                        $payment_details->save();

                        $new_paid_amount = 0;
                        break;
                    }
                }
            }
        } else {
            $payment = Payment::where('invoice_id', $request->invoice_type)->first();
            if ($payment->due_amount < $new_paid_amount) {
                $notification = array(
                    'message' => 'Paid amount is more than due amount of Invoice No:' . $request->invoice_type,
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
            }
            $payment_details = new PaymentDetail();
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
            $payment_details->invoice_id = $payment->invoice_id;

            $payment_details->customer_id = $request->customer_id;
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
    //Get invoice by customer
    public function getInvoicesByCustomer($id)
    {
        $invoices = Payment::where('customer_id', $id)->where('due_amount', '!=', 0)
            ->select('invoice_id', 'due_amount')->orderBy('due_amount', 'desc')
            ->get();
        return response()->json($invoices);
    }
    // Customer wise report
    public function CustomerReport(Request $request)
    {
        // dd($request->all());
        $customerId = $request->customer_id;

        // Fetch all payments for the customer
        $allData = Payment::where('customer_id', $customerId)->get();
        $total_due = $allData->sum('due_amount');
        // Get customer info from first use Customer model if available
        $customer = Customer::where('id', $customerId)->first();
        // Get distinct invoice IDs from payment records
        $invoiceIds = Payment::where('customer_id', $customerId)
            ->distinct()
            ->pluck('invoice_id');

        // Fetch and group payment details by created_at
        $paymentDetails = PaymentDetail::whereIn('invoice_id', $invoiceIds)->where('current_paid_amount','>', 0)
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
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at,
                'total_amount' => $details->sum('current_paid_amount'),
                'invoice_ids' => implode(', ', $details->pluck('invoice_id')->toArray()),
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

        return view('backend.customer.customer_report', compact('allData', 'customer', 'total_due', 'groupedDetails'));
    }
    // Customer Invoices Report Pdf
    public function CustomerInvoicesReportPdf($customerId)
    {
        // Fetch all payments for the customer
        $allData = Payment::where('customer_id', $customerId)->get();
        // Get customer info from first use Customer model if available
        $customer = Customer::where('id', $customerId)->first();
        return view('backend.customer.pdf.customer_invoice_report', compact('allData', 'customer'));
    } // End Method
    public function CustomerTransactionReportPdf($customerId)
    {
        // Get customer info from first use Customer model if available
        $customer = Customer::where('id', $customerId)->first();
        // Get distinct invoice IDs from payment records
        $invoiceIds = Payment::where('customer_id', $customerId)
            ->distinct()
            ->pluck('invoice_id');

        // Fetch and group payment details by created_at
        $paymentDetails = PaymentDetail::whereIn('invoice_id', $invoiceIds)->where('current_paid_amount','>', 0)
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
                'invoice_ids' => implode(', ', $details->pluck('invoice_id')->toArray()),
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

        return view('backend.customer.pdf.customer_transactions_report', compact('customer', 'groupedDetails'));
    } // End Method

    public function CustomerAllReportPdf($id = null)
    {
        if ($id != null) {
            $customers = Customer::where('id', $id)->latest()->get();
        } else {
            $customers = Customer::latest()->get();
        }
        return view('backend.customer.pdf.customer_all_report', compact('customers'));
    } // End Method

    public function CustomerAllTransaction(Request $request)
    {
        $customers_for_filter = Customer::latest()->get();
        $show_start_date = $request->get('startDate');
        $show_end_date = $request->get('endDate');
        $filter = $request->get('filter');
        $customer_filter = $request->get('customer_filter');
        // Handle date range
        if ($show_start_date && $show_end_date) {
            $startDate = Carbon::parse($request->get('startDate'));
            $endDate = Carbon::parse($request->get('endDate'))->endOfDay();
        } else {
            $startDate = today()->subDays(30)->startOfDay();
            $endDate = today()->endOfDay();
        }
        // Get distinct invoice IDs from payment records
        $invoiceQuery = Payment::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($customer_filter != null) {
            $invoiceQuery->where('customer_id', $customer_filter);
        }

        $invoiceIds = $invoiceQuery->pluck('invoice_id')->unique();
        // Fetch and group payment details by created_at
        $paymentDetails = PaymentDetail::whereIn('invoice_id', $invoiceIds)->where('current_paid_amount','>', 0)
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
                'customer_id' => $payment['customer']->id,
                'customer_name' => $payment['customer']->name,
                'customer_phone' => $payment['customer']->mobile_no,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at,
                'total_amount' => $details->sum('current_paid_amount'),
                'invoice_ids' => implode(', ', $details->pluck('invoice_id')->toArray()),
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
        return view('backend.customer.customer_all_transactions', compact(
            'customers_for_filter',
            'groupedDetails',
            'show_start_date',
            'show_end_date',
            'filter',
            'customer_filter'
        ));
    } // End Method
    public function CustomerDueMakePaymentEdit($transactionId)
    {
        $payments = PaymentDetail::with('customer')->where('transaction_id', $transactionId)->get();

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
        $invoice_ids = array_column($allPayments, 'invoice_id');

        // You can use this as an array or join it into a string
        $invoice_ids_string = implode(', ', $invoice_ids);

        // Sum current_paid_amount
        $total_paid = array_sum(array_column($allPayments, 'current_paid_amount'));
        // dd($common);
        // dd($differences);
        return view('backend.customer.customer_transaction_edit', compact('payments', 'common', 'differences', 'transactionId', 'invoice_ids_string', 'total_paid'));
    } // End Method
    
    public function CustomerDueMakePaymentUpdate(Request $request)
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
        $paymentDetails = PaymentDetail::where('transaction_id', $request->transaction_id)->get();
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
    } // End Method
    public function CustomerDueMakePaymentDelete($transactionId)
    {
        $payments = PaymentDetail::where('transaction_id', $transactionId)->get();
        
        return view('backend.customer.customer_transaction_edit', compact('payments', 'common', 'differences', 'transactionId', 'invoice_ids_string', 'total_paid'));
    } // End Method
    public function CustomerAllTransactionPdf($show_start_date = null, $show_end_date = null, $filter = 'null', $customer_filter = 'null')
    {
        $filter = $filter;
        $customer_filter = $customer_filter;
        // Handle date range
        if ($show_start_date && $show_end_date) {
            $startDate = Carbon::parse($show_start_date);
            $endDate = Carbon::parse($show_end_date)->endOfDay();
        } else {
            $startDate = today()->subDays(30)->startOfDay();
            $endDate = today()->endOfDay();
        }
        // Get distinct invoice IDs from payment records
        $invoiceQuery = Payment::query()
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($customer_filter != 'null') {
            $invoiceQuery->where('customer_id', $customer_filter);
        }
        $invoiceIds = $invoiceQuery->pluck('invoice_id')->unique();
        // dd($invoiceIds);
        // Fetch and group payment details by created_at
        $paymentDetails = PaymentDetail::whereIn('invoice_id', $invoiceIds)->where('current_paid_amount','>', 0)
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
                'customer_name' => $payment['customer']->name,
                'customer_phone' => $payment['customer']->mobile_no,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at,
                'total_amount' => $details->sum('current_paid_amount'),
                'invoice_ids' => implode(', ', $details->pluck('invoice_id')->toArray()),
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
        return view('backend.customer.pdf.all_customer_transactions_report', compact(
            'groupedDetails',
            'show_start_date',
            'show_end_date',
            'filter',
            'customer_filter'
        ));
    } // End Method
}
