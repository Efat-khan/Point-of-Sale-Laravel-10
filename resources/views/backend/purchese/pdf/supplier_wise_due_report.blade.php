@extends('admin.admin_master')
@section('admin')

<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Supplier Purchase Report</h4>
                    @php
                    $total_due = '0';
                    @endphp
                    @foreach($allData as $key => $item)
                    @php
                    $total_due += $item->due_amount;
                    @endphp
                    @endforeach
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 ">{{$total_due !=0?"SUPPLIER ALL DUE & PAYMENT":"ALL SUPPLIER PURCHASE REPORT"}}</li>
                            <li class=" active">
                                <a href="{{$total_due !=0?route('purchase.supplier_wise_purchese_payment.all'):route('purchase.wise.all.report')}}" name="supplier_id" class="btn btn-dark btn-rounded waves-effect waves-light" style="float:right;" title="Purchase Details">
                                    <i class="fa fa-chevron-circle-left"> Back </i>
                                </a>
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
                                <div class="row">
                                    <div class="col-6">
                                        <div class="invoice-title">

                                            <h3>
                                                <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="logo" height="24" /> Ovee Electric Enterprise
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="col-6 text-end d-print-none">
                                        <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light"><i class="fa fa-print"></i></a>
                                        <button type="button" class="btn btn-info " data-bs-target="#staticBackdrop" onclick="makePayment(
                                                '{{$supplier->supplier_id}}',
                                                '{{!empty($supplier["supplier"]["mobile_no"]) ? $supplier["supplier"]["mobile_no"] :"N/A" }}',
                                                '{{!empty($supplier["supplier"]["name"])? $supplier["supplier"]["name"] :"N/A" }}',
                                                '{{ $total_due }}')">
                                            Make Payment
                                        </button>
                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-6 mt-4">
                                        <address>
                                            <strong> Proprietor: Foyez Ullah Miazi</strong> <br>
                                            Munshirhat, Fulgazi, Feni <br>
                                            Mob: 01717323252
                                        </address>
                                    </div>
                                    <div class="col-6 mt-4 text-end">
                                        <address>

                                        </address>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div>
                                            <div class="p-2">
                                                <h3 class="font-size-16"><strong>Supplier Purchase</strong></h3>
                                            </div>
                                            <div class="">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <td><strong>Supplier Name</strong></td>
                                                                <td class="text-center"><strong>Mobile</strong></td>
                                                                <td class="text-center"><strong>Address</strong>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        <tr>
                                                            <td> {{ $supplier->supplier_id != -1 ? $supplier['supplier']['name'] : "Supplier"}}</td>
                                                            <td class="text-center">{{$supplier->supplier_id != -1 ? $supplier['supplier']['mobile_no'] :'N/A' }}</td>
                                                            <td class="text-center">{{$supplier->supplier_id != -1 ? $supplier['supplier']['address'] :'N/A' }}</td>
                                                        </tr>
                                                        <tbody>
                                                            <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div>
                                    <div class="p-2">
                                    </div>
                                    <div class="">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td colspan="2" class="text-start"><strong>Sl </strong>
                                                        </td>
                                                        <td colspan="2" class="text-start"><strong>Purchase No </strong>
                                                        </td>
                                                        <td colspan="2" class="text-start"><strong>Date</strong>
                                                        </td>
                                                        <td colspan="3" class="text-end"><strong>Paid Amount </strong>
                                                        </td>
                                                        <td colspan="3" class="text-end"><strong>Due Amount </strong>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- foreach ($order->lineItems as $line) or some such thing here -->

                                                    @php
                                                    $total_due = '0';
                                                    @endphp
                                                    @foreach($allData as $key => $item)
                                                    <tr>
                                                        <td colspan="2" class="text-start"> {{ $key+1}} </td>
                                                        <td colspan="2" class="text-start"><a href="{{ route('purchase.supplier.edit', $item->purchase_id) }}" class="" title="See Purchase"> #{{ $item->purchase_id }} </a></td>
                                                        <td colspan="2" class="text-start"> {{ date('d-m-Y',strtotime($item->created_at)) }} </td>
                                                        <td colspan="3" class="text-end">৳ {{ number_format($item->paid_amount,2) }} Tk</td>
                                                        <td colspan="3" class="text-end">৳ {{ number_format($item->due_amount,2) }} Tk</td>
                                                    </tr>
                                                    @php
                                                    $total_due += $item->due_amount;
                                                    @endphp
                                                    @endforeach

                                                    <tr>
                                                        <td colspan="6"></td>
                                                        <td colspan="3" class="text-end">
                                                            <strong>Grand Due Amount</strong>
                                                        </td>
                                                        <td colspan="3" class=" text-end">
                                                            <h4 class="m-0 text-danger">৳ {{ number_format($total_due,2)}} Tk</h4>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="12" style="text-align: center;font-weight: bold;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="12" style="text-align: center;font-weight: bold;">Payment Summary</td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="3" class="text-start" style="font-weight: bold;">Purchase No</td>
                                                        <td colspan="3" class="text-center" style="font-weight: bold;">Date </td>
                                                        <td colspan="3" class="text-start" style="font-weight: bold;"> </td>
                                                        <td colspan="3" class="text-end" style="font-weight: bold;">Paid Amount</td>
                                                    </tr>

                                                    @foreach($groupedDetails as $item)
                                                    <tr>
                                                        <td colspan="3" class="text-start" style="font-weight: bold;">
                                                            @php
                                                            $purchaseIdsArray = explode(',', $item['purchese_ids']);
                                                            @endphp

                                                            @if (is_array($purchaseIdsArray))
                                                            @foreach ($purchaseIdsArray as $purchaseId)
                                                            <a href="{{ route('purchase.supplier.edit', $purchaseId) }}" class="" title="See Purchase">
                                                                #{{ $purchaseId }}
                                                            </a>
                                                            @endforeach
                                                            @else
                                                            {{ $detail['purchese_ids'] }}
                                                            @endif

                                                        </td>
                                                        <td colspan="3" class="text-center" style="font-weight: bold;">{{ date('d-m-Y',strtotime($item['created_at'])) }}</td>
                                                        <td colspan="3" class="text-start" style="font-weight: bold;"></td>
                                                        <td colspan="3" class="text-end" style="font-weight: bold;">৳ {{ number_format($item['total_amount'],2) }} Tk</td>

                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @php
                                        $date = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                                        @endphp
                                        <i>Printing Time : {{ $date->format('F j, Y, g:i a') }}</i>
                                        
                                    </div>
                                </div>

                            </div>
                        </div> <!-- end row -->
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="{{route('purchase.supplier_wise_purchese_payment.make_payment')}}" method="POST">
                    @csrf
                    <input type="text" name="supplier_id" id="supplierId" hidden>
                    <div class="mb-3">
                        <h4 id="customerName"></h4>
                        <p>Mobile Number: <span id="supplier_mobile_number"></span></p>
                        <p>Total Due: ৳ <span id="totalDue"></span> Tk</p>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" id="date" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}" required>
                    </div>
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Bank</label>
                        <select id="disabledSelect" class="form-select" name="bank_name">
                            <option value="Hand Cash">Hand Cash</option>
                            <option value="Bkash">Bkash</option>
                            <option value="Nogod">Nagod</option>
                            <option value="Rocket">Rocket</option>
                            <option value="Bank Payment">Bank Payment</option>

                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="account_no" class="form-label">Account No./ Phone No.</label>
                        <input type="text" name="account_no" min="1" class="form-control" id="account_no">
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" name="due_amount" min="1" class="form-control" id="amount" hidden>
                        <input type="text" name="amount" min="1" class="form-control" id="amount" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Pay</button>
                </form>
            </div>

        </div>
    </div>
</div>
<script>
    function makePayment(id, supplierMobileNumber, supplierName, totalDue) {
        // Populate the modal with customer name and total due amount
        document.getElementById('customerName').textContent = supplierName;
        document.getElementById('supplier_mobile_number').textContent = supplierMobileNumber;
        document.getElementById('supplierId').value = id;
        document.getElementById('totalDue').textContent = totalDue;
        document.getElementById('amount').value = totalDue;
        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
        myModal.show();
    }
</script>
@endsection