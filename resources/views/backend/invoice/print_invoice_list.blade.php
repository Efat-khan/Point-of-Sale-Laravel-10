@extends('admin.admin_master')
@section('admin')

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Printed Invoice <a href="{{ route('invoice.add') }}" class="btn btn-dark btn-rounded waves-effect waves-light"><i class="fas fa-plus-circle"> </i> ADD </a></h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                            <li class="breadcrumb-item active"><a href="{{route('invoice.all')}}">ALL INVOICE</a></li>
                            <!-- <li class="breadcrumb-item active"><a href="{{route('invoice.add')}}">ADD INVOICE</a></li> -->
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
                            <h4 class="card-title col-10">Invoice All Data </h4> 
                        </div>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Customer</th>
                                    <th>Invoice No </th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>

                            </thead>


                            <tbody>

                                @foreach($allData as $key => $item)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ ($item['payment']['customer_id'] != -1)?$item['payment']['customer']['name']:'পথচারি কাস্টমার' }} </td>
                                    <td> {{ $item->invoice_no }} </td>
                                    <td> {{ date('d-m-Y',strtotime($item->date)) }} </td>
                                    <td> {{ $item->description }} </td>
                                    <td> ৳ {{ number_format($item['payment']['total_amount'],2) }} Tk</td>
                                    <td>
                                        <a href="{{ route('print.invoice',$item->id) }}" class="btn btn-danger sm" title="Print Invoice"> <i class="fas fa-print"></i> </a>
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


@endsection