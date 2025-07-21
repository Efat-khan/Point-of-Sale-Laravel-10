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
            <h4 class="mb-sm-0">STOCK REPORT </h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                    <li class="breadcrumb-item active"><a href="{{route('stock.report')}}">BACK</a></li>
                    <!-- <li class="breadcrumb-item active"><a href="{{route('invoice.add')}}">ADD INVOICE</a></li> -->
                </ol>
            </div>

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
                        <div class="row invoice-title">
                            <div class="col-10">
                                <h3>
                                    <img src="{{asset($org->logo)}}" alt="logo" height="24" /> {{ $org->org_name_en }}
                                </h3>
                                </h3>

                            </div>
                            <div class="col-2 " style="text-align:end">
                                <a href="javascript:window.print()" class="btn btn-success waves-effect waves-light"><i class="fa fa-print"></i></a>
                            </div>
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
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td class="text-center"><strong>Sl</strong></td>
                                                <td class="text-center"><strong>Brand</strong></td>
                                                <td class="text-center"><strong>Category</strong></td>
                                                <td class="text-center"><strong>Unit</strong></td>
                                                <td class="text-center"><strong>Product Name</strong></td>
                                                <!-- <td class="text-center"><strong>In Qty</strong></td> -->
                                                <td class="text-center"><strong>Out Qty</strong></td>
                                                <td class="text-center"><strong>Stock</strong></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- foreach ($order->lineItems as $line) or some such thing here -->

                                            @foreach($allData as $key => $item)
                                            @php

                                            $selling_total = App\Models\InvoiceDetail::where('category_id', $item->category_id)->where('product_id',$item->id)->where('status','1')->sum('selling_qty');
                                            @endphp
                                            <tr>

                                                <td class="text-center">{{$key+1}}</td>
                                                <td class="text-center">{{ $item['brand']['name'] ?? ''  }}</td>
                                                <td class="text-center">{{ $item['category']['name'] ?? ''  }}</td>

                                                <td class="text-center">{{ $item['unit']['name'] }}</td>
                                                <td class="text-center">{{ $item->name }}</td>
                                                <td class="text-center">{{ $selling_total }}</td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @php

                                $date = new DateTime('now', new DateTimeZone('Asia/Dhaka'));

                                @endphp

                                <i>Printing Time: {{ $date->format('F j, Y, g:i a')}}</i>



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