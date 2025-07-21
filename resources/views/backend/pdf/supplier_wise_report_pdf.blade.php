@extends('admin.admin_master')
@section('admin')
@php
$org = App\Models\OrgDetails::first();
@endphp
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Supplier Wise Report</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                            <li class="breadcrumb-item active">Supplier Wise Report</li>
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
                                <div class="invoice-title">
                                    <h3>
                                        <img src="{{asset($org->logo)}}" alt="logo" height="24" /> {{ $org->org_name_en }}
                                    </h3>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <address>
                                            Mob: {{ $org->mobile_no }} <br>
                                            Email: {{ $org->email }} <br>
                                            {{ $org->address}}
                                        </address>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div>

                                    <div class="">
                                        <div class="table-responsive">
                                            <h4 class="text-start"><strong>Supplier Name : {{ $allData['0']['supplier']['name'] }}</strong></h4>

                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td class="text-center ">
                                                            <h6><strong>Sl</strong></h6>
                                                        </td>
                                                        <!-- <td class="text-center"><strong>Supplier Name</strong></td> -->
                                                        <td class="text-center">
                                                            <h6><strong>Category</strong></h6>
                                                        </td>
                                                        <td class="text-center">
                                                            <h6><strong>Product Name</strong></h6>
                                                        </td>
                                                        <td class="text-center">
                                                            <h6><strong>Stock</strong></h6>
                                                        </td>
                                                        <td class="text-center">
                                                            <h6><strong>Buying Price</strong></h6>
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- foreach ($order->lineItems as $line) or some such thing here -->

                                                    @foreach($allData as $key => $item)
                                                    <tr>

                                                        <td class="text-center">{{$key+1}}</td>
                                                        <!-- <td class="text-center">{{ $item['supplier']['name'] }} </td> -->
                                                        <td class="text-center">{{ $item['category']['name']  }}</td>

                                                        <td class="text-center">{{ $item['product']['name'] }}</td>
                                                        <td class="text-center">{{ number_format($item->buying_qty) }}</td>
                                                        <td class="text-center">{{ number_format($item->buying_price,2) }} Tk</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @php

                                        $date = new DateTime('now', new DateTimeZone('Asia/Dhaka'));

                                        @endphp

                                        <i>Printing Time: {{ $date->format('F j, Y, g:i a')}}</i>


                                        <div class="d-print-none">
                                            <div class="float-end">
                                                <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light"><i class="fa fa-print"></i></a>
                                                <a href="#" class="btn btn-primary waves-effect waves-light ms-2">Download</a>
                                            </div>
                                        </div>
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
<!-- End Page-content -->

@endsection