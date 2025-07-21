@extends('admin.admin_master')
@section('admin')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Purchase</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                            <li class="breadcrumb-item active"><a href="{{route('purchase.all')}}">BACK</a></li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        @php
        $payment = App\Models\SupplierPurchesePayment::where('purchase_id',$purchase->id)->first();

        @endphp

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="row pt-4">
                            <div class="col-12">
                                <div class="invoice-title">
                                    <h4 class="float-end font-size-14">
                                        <strong> Munshirhat, Fulgazi, Feni</strong> <br>
                                        <!-- <strong>মোবাইল:</strong>  -->
                                        01717323252<br>
                                        <!-- <strong>Email:</strong>  -->

                                    </h4>
                                    <h3>
                                        <img class="report-logo" src="{{asset('backend/assets/images/logo-dark.png')}}" alt="logo" height="" />
                                    </h3>

                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <address>
                                            <strong>Billed To:</strong><br>
                                            {{ !empty($payment['supplier']['name']) ? $payment['supplier']['name'] : "N/A"}}<br>
                                            {{ !empty($payment['supplier']['mobile_no']) ? $payment['supplier']['mobile_no'] :'N/A' }}<br>
                                            {{ !empty($payment['supplier']['email']) ? $payment['supplier']['email'] :'N/A' }}<br>
                                            <!-- <h3 class="font-size-16"><strong>Customer Invoice</strong></h3> -->
                                        </address>
                                    </div>
                                    <div class="col-6 text-end">
                                        <address>
                                            <h4><strong>Purchase # {{$purchase->purchase_no}}</strong><br></h4>
                                            <strong>Date:</strong> {{date('d/m/y', strtotime($purchase->date))}}<br>
                                        </address>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- <div id="datatable-buttons_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"> --}}

                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <div class="p-2">
                                        <h3 class="font-size-16"><strong>Purchase</strong>
                                        </h3>
                                    </div>

                                    <div class="">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td><strong>Sl </strong></td>
                                                        <!-- <td class="text-center"><strong>Brand</strong></td> -->
                                                        <!-- <td class="text-center"><strong>Category</strong></td> -->
                                                        <td class="text-center"><strong>Product Name</strong>
                                                        </td>
                                                        <!-- <td class="text-center"><strong>Current Stock</strong> -->
                                                        </td>
                                                        <td class="text-center"><strong>Quantity</strong>
                                                        </td>
                                                        <td class="text-center"><strong>Unit Price </strong>
                                                        </td>
                                                        <td class="text-center"><strong>U.Price x Quantity</strong>
                                                        </td>
                                                        <td class="text-center"><strong>Total Discount(DB,MC,SP) </strong>
                                                        </td>
                                                        <td class="text-end"><strong>Total Price</strong>
                                                        </td>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                    @php
                                                    $total_sum = '0';
                                                    @endphp
                                                    @foreach($purchase['supplier_purchese_details'] as $key => $details)
                                                    <tr>
                                                        <td class="text-center">{{ $key+1 }}</td>
                                                        <!-- <td class="text-center">{{ (!empty($details['brand']['name'])?$details['brand']['name']:'Null') }}</td> -->
                                                        <!-- <td class="text-center">{{ (!empty($details['category']['name'])?$details['category']['name']:'Null') }}</td> -->
                                                        <td class="text-center">{{ $details['product']['name'] }} ({{ $details['brand']['name'] }})</td>
                                                        <!-- <td class="text-center">{{ number_format($details['product']['quantity']) }}</td> -->
                                                        <td class="text-center">{{ number_format($details->buying_qty) }}</td>
                                                        <td class="text-center">৳ {{ number_format($details->product_buying_price,1) }} Tk</td>
                                                        <td class="text-center">৳ {{ number_format($details->product_buying_price*$details->buying_qty,1) }} Tk</td>
                                                        <td class="text-center">৳ {{ number_format($details->total_db_com + $details->total_mc_com + $details->total_sp_com,1)}} Tk</td>
                                                        <td class="text-end">৳ {{ number_format($details->buying_price,1) }} Tk</td>

                                                    </tr>

                                                    @php
                                                    $total_sum += $details->buying_price;
                                                    @endphp
                                                    @endforeach

                                                    <tr>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line"></td>
                                                        <td class="thick-line text-end">
                                                            <strong>Subtotal</strong>
                                                        </td>
                                                        <td class="thick-line text-end">৳ {{ number_format($total_sum,1) }} Tk</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line text-end">
                                                            <strong>Discount</strong>
                                                        </td>
                                                        <td class="no-line text-end">৳ {{ number_format($payment->discount_amount,1) }} Tk</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line text-end ">
                                                            <strong>Total Payable</strong>
                                                        </td>
                                                        <td class="no-line text-end">
                                                            ৳ {{ number_format($payment->total_amount,1) }} Tk
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line text-end">
                                                            <strong> Paid</strong>
                                                        </td>
                                                        <td class="no-line text-end">৳ {{ number_format($payment->paid_amount,1) }} Tk</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line text-end">
                                                            <strong>Due</strong>
                                                        </td>
                                                        <td class="no-line text-end">৳ {{ number_format($payment->due_amount,1) }} Tk</td>
                                                    </tr>
                                                    <tr>

                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line text-end">
                                                            <strong class="text-danger">Previous Due</strong>
                                                        </td>
                                                        <td class="no-line text-danger text-end">৳ {{ number_format($pre_due,1) }} Tk</td>
                                                    </tr>
                                                    <tr>

                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line text-end">
                                                            <h6 class="text-danger">Total Due</h6>
                                                        </td>
                                                        <td class="no-line  text-end">
                                                            <h6 class="m-0 text-danger">৳ {{ number_format($pre_due + $payment->due_amount,1) }} Tk</h6>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="d-print-none">
                                            <div class="float-end">
                                                <button type="button" onclick="printDiv('printableArea')" class="btn btn-outline-info"><i class="fa fa-print"></i> POS</button>
                                                <!-- <button type="button" onclick="printDiv('printableArea')" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fa fa-print"></i> POS</button> -->
                                                <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light"><i class="fa fa-print"></i></a>
                                                <!-- <a href="#" class="btn btn-primary waves-effect waves-light ms-2">Download</a> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div> <!-- end row -->
                        {{-- End of Datatable's --}}
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">POS</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mx-auto" id="printableArea">
                            <style>
                                .dashed-hr {
                                    border-bottom: 2px dashed #dddddd;
                                    display: block;
                                    margin: 5px 0;
                                }

                                @page {
                                    size: auto;
                                    margin: 0 15px !important;
                                }

                                @media print {
                                    * {
                                        color: #000000 !important;
                                        font-weight: 500 !important;
                                    }

                                    h2,
                                    h3,
                                    h4,
                                    h5,
                                    h6 {
                                        font-weight: 700 !important;
                                    }

                                    .table {
                                        width: 100%;
                                        margin-bottom: 1rem;
                                        color: #000000;
                                        border-collapse: collapse;
                                    }

                                    .table td,
                                    .table th {
                                        padding: .75rem;
                                        vertical-align: top;
                                        border-top: 1px solid #000000
                                    }

                                    .table thead th {
                                        vertical-align: bottom;
                                        border-bottom: 1px solid #000000
                                    }

                                    .table tbody+tbody {
                                        border-top: 1px solid #000000
                                    }

                                    .table-sm td,
                                    .table-sm th {
                                        padding: .3rem
                                    }

                                    .table-bordered {
                                        border: 1px solid #000000
                                    }

                                    .table-bordered td,
                                    .table-bordered th {
                                        border: 1px solid #000000
                                    }

                                    .table-bordered thead td,
                                    .table-bordered thead th {
                                        border-bottom-width: 1px
                                    }

                                    .text-left {
                                        text-align: left !important;
                                    }

                                    .text-right {
                                        text-align: right !important;
                                    }

                                    .pl--0 {
                                        padding-left: 0 !important;
                                    }

                                    .pr--0 {
                                        padding-right: 0 !important;
                                    }

                                    /* .com-name{
                                                        font-size: 25px !important;
                                                        margin-bottom: -20px !important;
                                                    } */
                                }
                            </style>
                            <div class="col-md-12">
                                <div class="mx-auto" style="width:563px">
                                    <div class="text-center">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="invoice-title">

                                                    <div class="text-center mb-2">
                                                        {{-- <img class="report-logo" src="{{asset('backend/assets/images/logo-dark.png')}}" alt="logo" height=""/> --}}
                                                        <h3 class="com-name" style="font-size:25px;margin-bottom:-20px;">Ovee Electric Enterprise</h3> <br>
                                                        <strong style="margin-bottom:-10px;">Proprietor: Foyez Ullah Miazi</strong>
                                                    </div>
                                                    <h4 class="text-center font-size-14" style="margin-top:-7px;">
                                                        Munshirhat, Fulgazi, Feni <br>
                                                        <!-- <strong>মোবাইল:</strong>  -->
                                                        01717323252<br>
                                                        <!-- <strong>Email:</strong>  -->

                                                    </h4>
                                                </div>
                                                <span class="dashed-hr"></span>
                                                <div class="row">
                                                    <div class="col-6 text-start">
                                                        <address>
                                                            <h5><strong>Invoice # {{$purchase->purchase_no}}</strong><br></h5>
                                                            <strong>Date:</strong> {{date('d/m/y', strtotime($purchase->date))}}<br>
                                                        </address>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <address>
                                                            <strong>Billed To:</strong><br>
                                                            {{ !empty($payment['supplier']['name']) ? $payment['supplier']['name'] : "N/A"}}<br>
                                                            {{ !empty($payment['supplier']['mobile_no']) ? $payment['supplier']['mobile_no'] :'N/A' }}<br>
                                                            {{ !empty($payment['supplier']['email']) ? $payment['supplier']['email'] :'N/A' }}<br>
                                                        </address>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                    <span class="dashed-hr"></span>
                                    <style>
                                        #pos-print-demo,
                                        #pos-print-demo tr,
                                        #pos-print-demo td,
                                        #pos-print-demo th {
                                            border: 2px dashed #c6c6c6;
                                        }
                                    </style>
                                    <table id="pos-print-demo" class="table table-bordered text-left" style="width: calc(100% - 1px) !important">
                                        <thead>
                                            <tr>
                                                <th class="m-0 p-0"><strong>No.</strong></th>
                                                <th class="text-left m-0 p-0"><strong>Products</strong></th>
                                                <th class="m-0 p-0">
                                                    <center><strong>Pcs</strong></center>
                                                </th>
                                                <th class="m-0 p-0">
                                                    <center><strong>UP</strong></center>
                                                </th>
                                                <th class="m-0 p-0">
                                                    <center><strong>DB</strong></center>
                                                </th>
                                                <th class="m-0 p-0">
                                                    <center><strong>MC</strong></center>
                                                </th>
                                                <th class="m-0 p-0">
                                                    <center><strong>SC</strong></center>
                                                </th>
                                                <th class="m-0 p-0">
                                                    <center><strong>Price</strong></center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $sl = 0;
                                            $total_sum=0;
                                            @endphp
                                            @foreach($purchase['supplier_purchese_details'] as $key => $details)
                                            @php
                                            $sl++;
                                        @endphp
                                            <tr>
                                                <td class="m-0 p-0 text-start">{{$sl}}</td>
                                                <td class="m-0 p-0 text-start">{{ $details['product']['product_sort_name'] }} ({{ $details['brand']['name'] }})</td>
                                                {{-- <td class="m-0 p-0 text-center">{{ $details->selling_qty }} {{ $details['product']['unit']['name']}}</td> --}}
                                                <td class="m-0 p-0 text-center">{{ $details->buying_qty }}</td>
                                                <td class="m-0 p-0 text-center">{{ number_format($details->product_buying_price,1) }}</td>
                                                <td class="m-0 p-0 text-center">{{ number_format($details->total_db_com,1) }}</td>
                                                <td class="m-0 p-0 text-center">{{ number_format($details->total_mc_com,1) }}</td>
                                                <td class="m-0 p-0 text-center">{{ number_format($details->total_sp_com,1) }}</td>
                                                <td class="m-0 p-0 text-center">{{ number_format($details->buying_price,1) }}</td>
                                            </tr>
                                            @php
                                                $total_sum += $details->buying_price;
                                            @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <span class="dashed-hr"></span>
                                    <table style="color: black!important; width: 100%!important">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Total Price:</td>
                                                <td class="text-right">{{ number_format($total_sum,2) }} Tk</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Discount:</td>
                                                <td class="text-right">{{ number_format($payment->discount_amount,2) }} Tk</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Total Payable:</td>
                                                <td class="text-right">{{ number_format($payment->total_amount,2) }} Tk</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Paid:</td>
                                                <td class="text-right">{{ number_format($payment->paid_amount,2) }} Tk</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Due:</td>
                                                <td class="text-right">{{ number_format($payment->due_amount,2) }} Tk</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Previous Due:</td>
                                                <td class="text-right">{{ number_format($pre_due,2) }} Tk</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Total Due:</td>
                                                <td class="text-right">{{ number_format($pre_due+$payment->due_amount,2) }} Tk</td>
                                            </tr>


                                        </tbody>
                                    </table>

                                    <span class="dashed-hr"></span>
                                    <h5 class="text-center">
                                        Software: Munsoft BD, 01815229363
                                    </h5>
                                    <p class="text-center">
                                        <strong></strong>
                                        <span class="dashed-hr"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" onclick="printDiv('printableArea')">Print</button>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->

<script>
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>
@endsection