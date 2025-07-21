@extends('admin.admin_master')
@section('admin')

<div class="page-content">
                    <div class="container-fluid">
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Daily Return Report</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                                            <li class="breadcrumb-item active">Daily Return</li>
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
                                                        <img src="{{asset('backend/assets/images/logo-sm.png')}}" alt="logo" height="24"/> Ovee Electric Enterprise
                                                    </h3>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-6">
                                                    <address>
                                                             <strong> Proprietor: Foyez Ullah Miazi</strong> <br>
                                                            Munshirhat, Fulgazi, Feni <br>
                                                            Mob: 01717323252
                                                        </address>
                                                        
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <address>
                                                           
                                                        </address>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6 mt-4">
                                                        <address>
                                                            
                                                        </address>
                                                    </div>
                                                    <div class="col-6 mt-4 text-end">
                                                        <address>
                                                            
                                                        </address>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
        
                                        <div class="row">
                                            <div class="col-12">
                                                <div>
                                                    <div class="p-2">
                                                        <h3 class="font-size-16"><strong>Daily Purchase Report
                                                            <span class="btn btn-info"> {{date('d/m/y', strtotime($start_date))}}</span> -
                                                            <span class="btn btn-success"> {{date('d/m/y', strtotime($end_date))}}</span>
                                                        </strong></h3>
                                                    </div>
                                                    <div class="">
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                <tr>
                                                                    <td class="text-center"><strong>Sl</strong></td>
                                                                    <td class="text-center"><strong>Invoice No</strong></td>
                                                                    <td class="text-center"><strong>Date</strong></td>
                                                                    <td class="text-center"><strong>Product</strong></td>
                                                                    <td class="text-center"><strong>Description</strong></td>
                                                                    <td class="text-center"><strong>Quantity</strong></td>
                                                                    <td class="text-center"><strong>Unit Price</strong></td>
                                                                    <td class="text-center"><strong>Total Price</strong></td>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                                @php
                                                                    $total_sum = '0';
                                                                @endphp
                                                                @foreach($allData as $key => $item)
                                                                <tr>
                                                                    
                                                                    <td class="text-center">{{$key+1}}</td>
                                                                    <td class="text-center"># {{ $item->id }}</td>
                                                                    <td class="text-center">{{date('d/m/y', strtotime($item->date))}}</td>
                                                                    <td class="text-center">{{ $item['product']['name'] }}</td>
                                                                    <td class="text-center">{{ $item->description }}</td>
                                                                    <td class="text-center">{{ $item->return_qty }} {{$item['product']['unit']['name']}}</td>
                                                                    <td class="text-center">৳ {{ $item->unit_price }}</td>
                                                                    <td class="text-center">৳ {{ $item->return_price }}</td>
                                                                    
                                                                </tr>
                                                                @php
                                                                    $total_sum += $item->return_price;
                                                                @endphp
                                                                @endforeach
                                                               
                                                                <tr>
                                                                    <td class="no-line"></td>
                                                                    <td class="no-line"></td>
                                                                    <td class="no-line"></td>
                                                                    <td class="no-line"></td>
                                                                    <td class="no-line"></td>
                                                                    <td class="no-line"></td>
                                                                    <!-- <td class="no-line"></td> -->
                                                                    <td class="no-line text-center">
                                                                    <h4 class="m-0">Grand Total</h4></td>
                                                                    <td class="no-line text-Center"><h4 class="m-0">৳ {{ $total_sum }}</h4></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        @php

                                                            $date = new DateTime('now', new DateTimeZone('Asia/Dhaka'));

                                                        @endphp

                                                        <i>Printing Time: {{ $date->format('F j, Y, g:i a')}}. </i>
                                                        <i>Software generated report by <strong> Monsoft IT.</strong></i>
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