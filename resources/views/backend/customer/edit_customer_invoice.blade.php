@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Customer Invoice</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="">
                            <form action="{{route('customer.wise.due.report')}}" method="GET" id="myForm">
                                <button value="{{$payment->customer_id}}" name="customer_id" 
                                style=" background: none;border: none;padding: 0;font: inherit;
                                        color: inherit;
                                        text-decoration: none;
                                        cursor: pointer;" title="Customer Invoice Details"> 
                                        > BACK</button>
                            </form>
                            </li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <div class="p-2">
                                        <h3 class="font-size-16"><strong>Customer Invoice ( Invoice No: #{{ $payment['invoice']['invoice_no'] }} ) </strong></h3>
                                    </div>
                                    <div class="">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td><strong>Customer Name </strong></td>
                                                        <td class="text-center"><strong>Customer Mobile</strong></td>
                                                        <td class="text-center"><strong>Address</strong>
                                                        </td>


                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                    <tr>
                                                        <td> {{ $payment->customer_id !=-1 ?$payment['customer']['name']:'পথচারি কাস্টমার' }}</td>
                                                        <td class="text-center">{{ $payment->customer_id !=-1 ?$payment['customer']['mobile_no'] :'N/A' }}</td>
                                                        <td class="text-center">{{ $payment->customer_id !=-1 ?$payment['customer']['email']:'N/A'  }}</td>


                                                    </tr>


                                                </tbody>
                                            </table>
                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div> <!-- end row -->



                        <div class="row">
                            <div class="col-12">
                                <form method="post" action="{{ route('customer.update.invoice',$payment->invoice_id)}}">
                                    @csrf

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <td><strong>Sl </strong></td>
                                                    <td class="text-center"><strong>Category</strong></td>
                                                    <td class="text-center"><strong>Product Name</strong>
                                                    </td>
                                                    <td class="text-center"><strong>Current Stock</strong>
                                                    </td>
                                                    <td class="text-center"><strong>Quantity</strong>
                                                    </td>
                                                    <td class="text-center"><strong>Unit Price </strong>
                                                    </td>
                                                    <td class="text-center"><strong>Quantity x Unit Price </strong>
                                                    <td class="text-center"><strong>Discount</strong>
                                                    </td>
                                                    <td class="text-end"><strong>Total Price</strong>
                                                    </td>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- foreach ($order->lineItems as $line) or some such thing here -->

                                                @php
                                                $total_sum = '0';
                                                $invoice_details = App\Models\InvoiceDetail::where('invoice_id',$payment->invoice_id)->get();
                                                @endphp
                                                @foreach($invoice_details as $key => $details)
                                                <tr>
                                                    <td class="text-center">{{ $key+1 }}</td>
                                                    <td class="text-center">{{ (!empty($details['category']['name'])?$details['category']['name']:'Null') }}</td>
                                                    <td class="text-center">{{ $details['product']['name'] }}</td>
                                                    <td class="text-center">{{ $details['product']['quantity'] }}</td>
                                                    <td class="text-center">{{ $details->selling_qty }}</td>
                                                    <td class="text-center">{{ number_format($details->unit_price,2) }} Tk</td>
                                                    <td class="text-center">{{ number_format($details->selling_qty*$details->unit_price,2) }} Tk</td>
                                                    <td class="text-center">{{ number_format($details->total_sell_commission,2) }} Tk</td>
                                                    <td class="text-end">{{ number_format($details->selling_price,2) }} Tk</td>

                                                </tr>
                                                @php
                                                $total_sum += $details->selling_price;
                                                @endphp
                                                @endforeach
                                                <tr>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line text-center">
                                                        <strong>Subtotal</strong>
                                                    </td>
                                                    <td class="thick-line text-end">৳{{ number_format($total_sum,2) }} Tk</td>
                                                </tr>
                                                <tr>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line text-center">
                                                        <strong>Discount Amount</strong>
                                                    </td>
                                                    <td class="no-line text-end">৳{{ number_format($payment->discount_amount,2) }} Tk</td>
                                                </tr>
                                                <tr>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line text-center">
                                                        <h4 class="m-0">Total Payable</h4>
                                                    </td>
                                                    <td class="no-line text-end">
                                                        <h4 class="m-0">৳{{ number_format($payment->total_amount,2) }} Tk</h4>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line text-center">
                                                        <strong>Paid Amount</strong>
                                                    </td>
                                                    <td class="no-line text-end">৳{{ number_format($payment->paid_amount,2) }} Tk</td>
                                                </tr>

                                                <tr>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line text-center">
                                                        <h4 class="text-danger">Due Amount</h4>
                                                    </td>
                                                    <input type="hidden" name="new_paid_amount" value="{{$payment->due_amount }}">
                                                    <td class="no-line text-end">
                                                        <h4 class="text-danger">৳{{ number_format($payment->due_amount,2) }} Tk</h4>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- ############## Payment update select button ############## -->

                                    <div class="row">

                                        <div class="form-group col-md-3">
                                            <label> Paid Status </label>
                                            <select name="paid_status" id="paid_status" class="form-select">
                                                <option value="">Select Status </option>
                                                <option value="full-paid">Full Paid </option>
                                                <option value="partial-paid">Partial Paid </option>
                                            </select>
                                            <br>
                                            <input type="text" name="paid_amount" class="form-control paid_amount" placeholder="Enter Paid Amount" style="display:none;">
                                        </div>


                                        <div class="form-group col-md-3">
                                            <div class="md-3">
                                                <label for="example-text-input" class="form-label">Date</label>
                                                <input class="form-control example-date-input" placeholder="YYYY-MM-DD" name="date" type="date" id="date">
                                            </div>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <div class="md-3" style="padding-top: 30px;">
                                                <button type="submit" class="btn btn-info">Invoice Update</button>
                                            </div>

                                        </div>

                                    </div>
                            </div> <!-- end row -->

                            <!-- ############## End Payment update select button ############## -->



                        </div>



                    </div>

                    </form>

                </div>

            </div> <!-- end row -->

        </div>

    </div>
</div> <!-- end col -->
</div> <!-- end row -->

</div> <!-- container-fluid -->
</div>


<script type="text/javascript">
    $(document).on('change', '#paid_status', function() {
        var paid_status = $(this).val();
        if (paid_status == 'partial-paid') {
            $('.paid_amount').show();
        } else {
            $('.paid_amount').hide();
        }
    });
</script>


@endsection