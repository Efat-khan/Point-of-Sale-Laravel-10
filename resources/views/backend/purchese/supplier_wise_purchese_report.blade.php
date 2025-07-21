@extends('admin.admin_master')
@section('admin')


<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">All Supplier Purchase Report <a href="{{ route('purchase.add') }}" class="btn btn-dark btn-rounded waves-effect waves-light"><i class="fas fa-plus-circle"> </i> ADD </a></h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                            <li class="breadcrumb-item active"><a href="{{route('purchase.all')}}">ALL PURCHASE</a></li>
                            <li class="breadcrumb-item active"><a href="{{route('purchase.supplier_wise_purchese_payment.all')}}">SUPPLIER ALL DUE & PAYMENT</a></li>
                            <!-- <li class="breadcrumb-item active"><a href="{{route('purchase.wise.all.report')}}">ALL SUPPLIER PURCHASE REPORT</a></li> -->
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
                        <!-- <a href="{{route('credit.customer.print.pdf')}}" class="btn btn-dark btn-rounded waves-effect waves-light" style="float:right;"><i class="fas fa-print"> Print Credit Customer </i> </a> <br> <br> -->

                        <!-- <h4 class="card-title">Credit Customer All Data </h4> -->

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20px;">#</th>
                                    <th>Supplier Name</th>
                                    <th>Supplier Phone</th>
                                    <!-- <th>Total Payment</th> -->
                                    <th>Total Due</th>
                                    <th width="30px;">Action</th>
                            </thead>
                            <tbody>
                                @foreach($supplier_purchase_and_payment_infos as $key => $info)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ !empty($info->name)?$info->name:'N/A' }} </td>

                                    <td> {{ !empty($info->mobile_no)?$info->mobile_no:''}} </td>
                                    <!-- <td> ৳ {{ $info->total_paid}} Tk</td> -->
                                    <td> ৳ {{ !empty($info->total_due)?number_format( $info->total_due,2):'0.00'}} Tk</td>

                                    <td>
                                        <form action="{{route('purchase.wise.due.report')}}" method="GET" id="myForm">
                                            <button value="{{$info->id}}" name="supplier_id" class="btn btn-danger sm" target="_blank" title="Purchase Details"> <i class="fas fa-eye"></i> </button>

                                        </form>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
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