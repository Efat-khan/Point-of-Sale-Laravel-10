@extends('admin.admin_master')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="card-title">Delivery Zone Details Report </h4><br><br>

                            <form method="GET" action="{{ route('deliveryzone.invoice.details') }}">
                                <div class="row">

                                    <div class="col-md-3 my-1">
                                        <div class="md-3">
                                            <label for="example-text-input" class="form-label">Delivery Zone</label>
                                            <select class="form-control select2-selection__rendered select2 multiple"
                                                name="delivery_zone[]" multiple>
                                                <option value="">Select</option>
                                                @foreach (\App\Models\DeliveryZone::orderBy('id', 'ASC')->get() as $zone)
                                                    <option value="{{ $zone->id }}"
                                                        {{ is_array(request()->input('delivery_zone')) && in_array($zone->id, request()->input('delivery_zone')) ? 'selected' : '' }}>
                                                        {{ $zone->delivery_zone }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 my-1">
                                        <div class="md-3">
                                            <label for="example-text-input" class="form-label">SR</label>
                                            <select class="form-control select2-selection__rendered select2 multiple"
                                                name="sr[]" multiple>
                                                <option value="">Select</option>
                                                @foreach (\App\Models\SalesRep::where('status', 1)->orderBy('name', 'ASC')->get() as $sr)
                                                    <option value="{{ $sr->id }}"
                                                        {{ is_array(request()->input('sr')) && in_array($sr->id, request()->input('sr')) ? 'selected' : '' }}>
                                                        {{ $sr->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 my-1">
                                        <div class="md-3">
                                            <label for="example-text-input" class="form-label">Start Date</label>
                                            <input class="form-control example-date-input" name="start_date" type="date"
                                                value="{{ $s_date }}" id="start_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2 my-1">
                                        <div class="md-3">
                                            <label for="example-text-input" class="form-label">End Date</label>
                                            <input class="form-control example-date-input" name="end_date" type="date"
                                                id="end_date" value="{{ $e_date }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2 my-1">
                                        <div class="md-3">
                                            <label for="example-text-input" class="form-label"
                                                style="margin-top: 43px;"></label>
                                            <button type="submit" class="btn btn-info">Search</button>
                                        </div>
                                    </div>
                                </div> <!-- // end row  -->
                            </form>

                        </div> <!-- End card-body -->
                        <!--  ---------------------------------- -->

                    </div>
                </div> <!-- end col -->
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-12">
                                    <div class="invoice-title">
                                        <h3>
                                            <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="logo"
                                                height="24" /> Ovee Electric Enterprise
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
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @php
                                $_g_total_qty = 0;
                                $_g_total_up = 0;
                                $_g_total_sc = 0;
                                $_g_total_am = 0;
                            @endphp
                            @forelse ($search_data as $zone)
                                @foreach ($zone->groupBy('sales_rep_id') as $sr)
                                    <div class="row">
                                        <div class="col-12">
                                            <div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="p-2">
                                                            <h3 class="font-size-16">
                                                                <span>
                                                                    <b>ডেলিভারি জোন:</b>
                                                                    {{ $sr->first()?->delivery_zones?->delivery_zone }}
                                                                    <br>
                                                                    <b> SR:</b> {{ $sr->first()?->sales_rep?->name }}
                                                                    {{-- @if (request()->input('start_date') && request()->input('end_date'))
                                                                        <br>
                                                                        Date:
                                                                        <b>{{ date('d/m/y', strtotime(request()->input('start_date'))) }}
                                                                            -
                                                                            {{ date('d/m/y', strtotime(request()->input('end_date'))) }}</b>
                                                                    @endif --}}
        
                                                                    <br>
                                                                    <b>Date:</b>
                                                                    {{ date('d/m/y', strtotime(request()->input('start_date'))) }}
                                                                        -
                                                                        {{ date('d/m/y', strtotime(request()->input('end_date'))) }}
                                                                
                                                                </span>
                                                            </h3>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <a href="{{ route('deliveryzone.invoice.summary') }}" class="btn btn-dark btn-rounded waves-effect waves-light" style="float:right;"><i class="fas fa-chevron-circle-left"> Delivery Summary </i> </a>
                                                    </div>
                                                </div>

                                                
                                                <div class="">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered ">
                                                            <thead>
                                                                <tr class="table-primary">
                                                                    <td class="text-center"><strong>Sl</strong></td>
                                                                    <td class="text-center"><strong>Invoice No</strong>
                                                                    </td>
                                                                    <td class="text-center"><strong>Date</strong></td>
                                                                    <td class="text-center"><strong>Customer Name</strong></td>
                                                                    <td class="text-center"><strong>Product</strong></td>
                                                                    <td class="text-center"><strong>Quantity</strong></td>
                                                                    <td class="text-center"><strong>Unit Price</strong></td>
                                                                    <td class="text-center"><strong>Sell Commission</strong></td>
                                                                    <td class="text-center"><strong>Amount</strong></td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                                @php
                                                                    $Sl = 0;
                                                                    $_total_qty = 0;
                                                                    $_total_up = 0;
                                                                    $_total_sc = 0;
                                                                    $_total_am = 0;
                                                                @endphp
                                                                @foreach ($sr as $Data)
                                                                    @foreach ($Data->invoice_details as $key => $item)
                                                                        @php
                                                                            $_total_qty += $item->selling_qty;
                                                                            $_total_up += $item->unit_price;
                                                                            $_total_sc += $item->total_sell_commission;
                                                                            $_total_am += $item->selling_price;
                                                                        @endphp
                                                                        <tr>
                                                                            @if ($key == 0)
                                                                                <td rowspan="{{$Data->invoice_details->count()}}">{{ ++$Sl }}</td>
                                                                                <td rowspan="{{$Data->invoice_details->count()}}">{{ $Data->invoice_no }}</td>
                                                                                <td rowspan="{{$Data->invoice_details->count()}}">{{ !empty($Data->date) ? date('d-M-Y', strtotime($Data->date)) : '' }}
                                                                                </td>
                                                                                <td rowspan="{{$Data->invoice_details->count()}}">{{ $Data?->payment?->customer?->name }}</td>
                                                                            @endif
                                                                            <td>{{ $item->product?->name }}</td>
                                                                            <td>{{ $item->selling_qty }}</td>
                                                                            <td>{{ $item->unit_price }}</td>
                                                                            <td>{{ $item->total_sell_commission }}</td>
                                                                            <td>{{ $item->selling_price }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            </tbody>
                                                            @php
                                                                $_g_total_qty += $_total_qty;
                                                                $_g_total_up += $_total_up;
                                                                $_g_total_sc += $_total_sc;
                                                                $_g_total_am += $_total_am;
                                                            @endphp
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="5">
                                                                        <h5>Total</h5>
                                                                    </td>

                                                                    <td class="no-line text-Center">
                                                                        <h4 class="m-0">{{ $_total_qty }}</h4>
                                                                    </td>
                                                                    <td class="no-line text-Center">
                                                                        <h4 class="m-0">{{ $_total_up }}</h4>
                                                                    </td>
                                                                    <td class="no-line text-Center">
                                                                        <h4 class="m-0">{{ $_total_sc }}</h4>
                                                                    </td>
                                                                    <td class="no-line text-Center">
                                                                        <h4 class="m-0">{{ $_total_am }}</h4>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                    {{-- <div class="d-print-none">
                                                        <div class="float-end">
                                                            <a href="javascript:window.print()"
                                                                class="btn btn-success waves-effect waves-light"><i
                                                                    class="fa fa-print"></i></a>
                                                            <a href="#"
                                                                class="btn btn-primary waves-effect waves-light ms-2">Download</a>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            @empty
                                <h2 class="text-center text-secondary">No Data Found!</h2>
                            @endforelse
                            <hr>
                            @if ($search_data->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered ">
                                        <tr>
                                            <th>Total Quantity</th>
                                            <td>{{ $_g_total_qty }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Unit Price</th>
                                            <td>{{ $_g_total_up }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Sell Commission</th>
                                            <td>{{ $_g_total_sc }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Amount</th>
                                            <td>{{ $_g_total_am }}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                            @endif

                            <!-- end row -->

                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->


        </div>
    </div>
@endsection
