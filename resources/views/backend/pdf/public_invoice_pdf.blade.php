<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard | Ovee Electric Enterprise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" >
+
</head>
<body>
    <div class="page-content">
        <div class="container">

            <!-- end page title -->
            @php
                 $payment = App\Models\Payment::where('invoice_id',$invoice->id)->first();
            @endphp

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice-title">
                                        <h4 class="float-end font-size-14">
                                           <strong> Munshirhat, Fulgazi, Feni</strong> <br>
                                            <!-- <strong>মোবাইল:</strong>  -->
                                            01717323252<br>
                                            <!-- <strong>Email:</strong>  -->
                                            
                                        </h4>                                                   
                                        <h3>
                                            <img class="report-logo" src="{{asset('backend/assets/images/logo-dark.png')}}" alt="logo" height="80"/>  
                                        </h3>
                                        
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                           <address>
                                                <strong>Billed To:</strong><br>
                                                {{ $payment['customer']['name']}}<br>
                                                {{ $payment['customer']['mobile_no']}}<br>
                                                {{ $payment['customer']['email']}}<br>
                                                <!-- <h3 class="font-size-16"><strong>Customer Invoice</strong></h3> -->
                                            </address>
                                        </div>
                                        <div class="col-6 text-end">
                                           <address>
                                                <h4><strong>Invoice # {{$invoice->invoice_no}}</strong><br></h4>
                                                <strong>Date:</strong>                                   {{date('d/m/y', strtotime($invoice->date))}}<br>
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
                                            <h3 class="font-size-16"><strong>Customer Invoice</strong>
                                           </h3>
                                        </div>
                                        {{-- ssss --}}
                                        {{-- <div class="col-sm-12 col-md-6"><div class="dt-buttons btn-group flex-wrap">      <button class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="datatable-buttons" type="button"><span>Copy</span></button> <button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="datatable-buttons" type="button"><span>Excel</span></button> <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="datatable-buttons" type="button"><span>PDF</span></button> <div class="btn-group"><button class="btn btn-secondary buttons-collection dropdown-toggle buttons-colvis" tabindex="0" aria-controls="datatable-buttons" type="button" aria-haspopup="true" aria-expanded="false"><span>Column visibility</span></button></div> </div></div> --}}
                                        {{-- eeee --}}
                                            <div class="">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                        <tr>
                                                            <td class="text-center"><strong>Sl</strong></td>
                                                            {{-- <td class="text-center"><strong>Category</strong></td> --}}
                                                            <td class="text-center"><strong>Product Name</strong></td>
                                                            <td class="text-center"><strong>Quantity</strong></td>
                                                            <td class="text-center"><strong>Unit Price</strong></td>
                                                            <td class="text-center"><strong>Sell Commission</strong></td>
                                                            <td class="text-center"><strong>Total Pirce</strong></td>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                        @php
                                                            $total_sum = '0';
                                                        @endphp
                                                        @foreach($invoice['invoice_details'] as $key => $details)
                                                        <tr>
                                                            
                                                            <td class="text-center">{{$key+1}}</td>
                                                            {{-- <td class="text-center">{{ $details['category']['name'] }}</td> --}}
                                                            <td class="text-center">{{ $details['product']['name'] }}</td>
                                                            <!-- <td class="text-center" style="background-color: #8B008B">{{ $details['product']['quantity'] }}</td> -->
                                                            <td class="text-center">{{ $details->selling_qty }} {{ $details['product']['unit']['name']}}</td>
                                                            <td class="text-center">৳ {{ $details->unit_price }}</td>
                                                            <td class="text-center">৳ {{ $details->total_sell_commission }}</td>
                                                            <td class="text-center">৳ {{ $details->selling_price }}</td>
                                                        </tr>
                                                       
                                                        @php
                                                            $total_sum += $details->selling_price;
                                                        @endphp
                                                        @endforeach
                                                       
                                                        <tr>
                                                            <td colspan="4" class="thick-line"></td>
                                                            <td class="thick-line text-center">
                                                                <strong>Subtotal</strong></td>
                                                            <td class="thick-line text-center">৳ {{ $total_sum }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="thick-line"></td>
                                                            <td class="no-line text-center">
                                                                <strong>Discount</strong></td>
                                                            <td class="no-line text-center">৳ {{ $payment->discount_amount }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="thick-line"></td>
                                                            <td class="no-line text-center">
                                                                <strong>Total Payable</strong></td>
                                                            <td class="no-line text-Center"><h6 class="m-0 text-center">৳ {{ $payment->total_amount }}</h6></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="thick-line"></td>
                                                            <td class="no-line text-center">
                                                                <strong>Cash Paid</strong></td>
                                                            <td class="no-line text-center">৳ {{ $payment->paid_amount }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="thick-line"></td>
                                                            <td class="no-line text-center">
                                                                <strong>Due</strong></td>
                                                            <td class="no-line text-center">৳ {{ $payment->due_amount }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="thick-line"></td>
                                                            <td class="no-line text-center">
                                                                <strong class="text-danger">Previous Due</strong></td>
                                                            <td class="no-line text-danger text-center">৳ {{ $pre_due }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="d-print-none">
                                                    <div class="float-end">
                                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fa fa-print"></i> POS</button>
                                                        <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light">Print</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div> <!-- end row -->

                            {{-- </div> --}}
                            {{-- End of Datatables --}}

                            

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
                                    .dashed-hr{
                                        border-bottom: 2px dashed #dddddd;
                                        display: block;
                                        margin: 10px 0;
                                    }
                                    @page  {
                                        size: auto;
                                        margin: 0 15px !important;
                                    }
                                    @media  print {
                                        * {
                                            color: #000000 !important;
                                            font-weight: 500 !important;
                                        }
                                        h2,h3,h4,h5,h6{
                                            font-weight: 700 !important;
                                        }
                                        .table {
                                            width: 100%;
                                            margin-bottom: 1rem;
                                            color: #000000;
                                            border-collapse: collapse;
                                        }
                                
                                        .table td, .table th {
                                            padding: .75rem;
                                            vertical-align: top;
                                            border-top: 1px solid #000000
                                        }
                                
                                        .table thead th {
                                            vertical-align: bottom;
                                            border-bottom: 1px solid #000000
                                        }
                                
                                        .table tbody + tbody {
                                            border-top: 1px solid #000000
                                        }
                                
                                        .table-sm td, .table-sm th {
                                            padding: .3rem
                                        }
                                
                                        .table-bordered {
                                            border: 1px solid #000000
                                        }
                                
                                        .table-bordered td, .table-bordered th {
                                            border: 1px solid #000000
                                        }
                                
                                        .table-bordered thead td, .table-bordered thead th {
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
                                    }
                                </style>
                                <div class="col-md-12">
                                    <div class="mx-auto" style="width:363px">
                                        <div class="text-center pt-4 mb-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="invoice-title">
                                                                                                          
                                                        <div class="text-center mb-2">
                                                            <img class="report-logo" src="{{asset('backend/assets/images/logo-dark.png')}}" alt="logo" height="50"/>  
                                                        </div>
                                                        <h6 class="text-center">
                                                            <strong> Munshirhat, Fulgazi, Feni</strong> <br>
                                                                <!-- <strong>মোবাইল:</strong>  -->
                                                                01717323252<br>
                                                                <!-- <strong>Email:</strong>  -->
                                                                
                                                            </h6> 
                                                    </div>
                                                    <span class="dashed-hr"></span>
                                                    <div class="row">
                                                        <div class="col-6 text-start">
                                                            <address>
                                                                    <h5><strong>Invoice # {{$invoice->invoice_no}}</strong><br></h5>
                                                                    <strong>Date:</strong>                                   {{date('d/m/y', strtotime($invoice->date))}}<br>
                                                                </address>
                                                            </div>
                                                        <div class="col-6 text-end">
                                                        <address>
                                                                <strong>Billed To:</strong><br>
                                                                {{ $payment['customer']['name']}}<br>
                                                                {{ $payment['customer']['mobile_no']}}<br>
                                                                {{ $payment['customer']['email']}}<br>
                                                                <!-- <h3 class="font-size-16"><strong>Customer Invoice</strong></h3> -->
                                                            </address>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            
                                        </div>
                                    
                                        <span class="dashed-hr"></span>
                                        <style>
                                            #pos-print-demo,#pos-print-demo tr,#pos-print-demo td,#pos-print-demo th{
                                                border:2px dashed #c6c6c6;
                                            }
                                        </style>
                                        <table id="pos-print-demo" class="table table-bordered mt-3 text-left" style="width: calc(100% - 1px) !important">
                                            <thead>
                                            <tr>
                                                <th class="text-left m-0 p-0">Products Name</th>
                                                <th class="m-0 p-0"><center>QTY</center></th>
                                                <th class="m-0 p-0"><center>UP</center></th>
                                                <th class="m-0 p-0"><center>SC</center></th>
                                                <th class="m-0 p-0"><center>Price</center></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $sl = 0; 
                                                @endphp
                                                @foreach($invoice['invoice_details'] as $key => $details)  
                                                @php
                                                    $sl++; 
                                                @endphp                                                       
                                                    <tr>
                                                        <td class="m-0 p-0 text-start">{{$sl}}</td>
                                                        <td class="m-0 p-0 text-start">{{ $details['product']['product_sort_name'] }}</td>
                                                        <td class="m-0 p-0 text-center">{{ $details->selling_qty }} {{ $details['product']['unit']['name']}}</td>
                                                        <td class="m-0 p-0 text-center">{{ $details->unit_price }}</td>
                                                        <td class="m-0 p-0 text-center">{{ $details->total_sell_commission }}</td>
                                                        <td class="m-0 p-0 text-center">{{ $details->selling_price }}</td>
                                                    </tr>
                                                @endforeach                              
                                            </tbody>
                                        </table>
                                        <span class="dashed-hr"></span>
                                            <table style="color: black!important; width: 100%!important">
                                            <tbody><tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Total Price:</td>
                                                <td class="text-right">{{ $total_sum }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Discount:</td>
                                                <td class="text-right">{{ $payment->discount_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Total Payable:</td>
                                                <td class="text-right">{{ $payment->total_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Cash Paid:</td>
                                                <td class="text-right">{{ $payment->paid_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Due:</td>
                                                <td class="text-right">{{ $payment->due_amount }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="text-right">Previous Due:</td>
                                                <td class="text-right">{{ $pre_due }}</td>
                                            </tr>
                                            
                                        </tbody></table>
                                    
                                        <span class="dashed-hr"></span>
                                        <h5 class="text-center pt-3">
                                            """THANK YOU"""
                                        </h5>
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
    <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>


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
</body>
</html>


