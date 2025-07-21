@extends('admin.admin_master')
@section('admin')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="card-title">Delivery Zone Summary </h4><br><br>

                            <form method="GET" action="{{ route('deliveryzone.invoice.summary') }}">
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
                                                                   <b> ডেলিভারি জোন: </b>
                                                                    {{ $sr->first()?->delivery_zones?->delivery_zone }}
                                                                    <br>
                                                                    <b> SR: </b> {{ $sr->first()?->sales_rep?->name }}
                                                                    {{-- @if (request()->input('start_date') && request()->input('end_date'))
                                                                        <br>
                                                                        <b>Date:</b>
                                                                        {{ date('d/m/y', strtotime(request()->input('$start_date'))) }}
                                                                            -
                                                                            {{ date('d/m/y', strtotime(request()->input('$end_date'))) }}</b>
                                                                    @endif --}}
                                                                        <br>
                                                                        <b>Date:</b>
                                                                        {{ date('d/m/y', strtotime(request()->input('$start_date'))) }}
                                                                        -
                                                                        {{ date('d/m/y', strtotime(request()->input('$end_date'))) }}
                                                                    
                                                                </span>
                                                            </h3>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <a href="{{ route('deliveryzone.invoice.summary.edit') }}" class="btn btn-dark btn-rounded waves-effect waves-light" style="float:right;"><i class="far fa-edit"> Delivery Summary Edit </i> </a>
                                                        <a href="{{ route('deliveryzone.invoice.details') }}" class="btn btn-dark btn-rounded waves-effect waves-light" style="float:right;"><i class="fas fa-list"> Delivery Details </i> </a>
                                                    </div>
                                                </div>

                                                <div class="">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered report-table">
                                                            <thead>
                                                                <tr class="table-primary">
                                                                    <td class="text-center"><strong>Sl</strong></td>
                                                                    <td class="text-center"><strong>Product</strong></td>
                                                                    <td class="text-center"><strong>Order Quantity</strong></td>
                                                                    <td class="text-center"><strong>Unit Price</strong></td>
                                                                    <td class="text-center"><strong>Sells Commission</strong></td>
                                                                    <td class="text-center"><strong>Amount</strong></td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $Sl = 0;
                                                                    $_total_qty = 0;
                                                                    $_total_up = 0;
                                                                    $_total_sc = 0;
                                                                    $_total_am = 0;
                                                                    $products = [];
                                                                @endphp
                                                                @foreach ($sr as $Data)
                                                                    @foreach ($Data->invoice_details as $key => $item)
                                                                        @php
                                                                            
                                                                            if(isset($products[$item->product_id])){
                                                                                $products[$item->product_id]['name'] = $item->product?->name;
                                                                                $products[$item->product_id]['selling_qty'] += $item->selling_qty;
                                                                                $products[$item->product_id]['unit_price'] = $item->unit_price;
                                                                                $products[$item->product_id]['total_sell_commission'] += $item->total_sell_commission;
                                                                            }else{
                                                                                $products[$item->product_id] = [
                                                                                    'name' =>$item->product?->name,
                                                                                    'selling_qty' =>$item->selling_qty,
                                                                                    'unit_price' =>$item->unit_price,
                                                                                    'total_sell_commission' =>$item->total_sell_commission,
                                                                                ];
                                                                            }
                                                                        @endphp
                                                                    @endforeach
                                                                @endforeach
                                                                @foreach($products as $product)
                                                                @php
                                                                    $_total_qty += $product["selling_qty"] ?? 0;
                                                                    $_total_up += $product["unit_price"] ?? 0;
                                                                    $_total_sc += $product["total_sell_commission"] ?? 0;
                                                                    $_total_am += (($product["selling_qty"] *  $product["unit_price"]) - $product["total_sell_commission"]);
                                                                @endphp
                                                                    <tr>
                                                                        <input type="hidden" class="form-control" name="unit_price[]" value="{{$product["unit_price"]??''}}" style="width:100px;"/>
                                                                        <input type="hidden" class="form-control" name="total_sell_commission[]" value="{{$product["total_sell_commission"]??''}}" style="width:100px;"/>
                                                                        <td>{{ ++$Sl }} </td>
                                                                        <td>{{ $product["name"]??'' }}</td>
                                                                        <td>{{ $product["selling_qty"]??'' }}</td>
                                                                        <td>{{ $product["unit_price"]??'' }}</td>
                                                                        <td>{{ $product["total_sell_commission"]??'' }}</td>
                                                                        <td class="row-selling_price">{{ (($product["selling_qty"] *  $product["unit_price"]) - $product["total_sell_commission"]) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            @php
                                                                $_g_total_qty += $_total_qty;
                                                                $_g_total_up += $_total_up;
                                                                $_g_total_sc += $_total_sc;
                                                                $_g_total_am += $_total_am;
                                                            @endphp
                                                            <tfoot>
                                                                <tr class="tfoot-row">
                                                                    <td colspan="2">
                                                                        <h5>Total</h5>
                                                                    </td>

                                                                    <td class="no-line text-Center">
                                                                        <h4 class="m-0">{{ $_total_qty }}</h4>
                                                                    </td>
                                                                    <td class="no-line text-Center">
                                                                        <h4 class="m-0 total-unit-price">{{ $_total_up }}</h4>
                                                                    </td>
                                                                    <td class="no-line text-Center">
                                                                        <h4 class="m-0 total-commission">{{ $_total_sc }}</h4>
                                                                    </td>
                                                                    <td class="no-line text-Center">
                                                                        <h4 class="m-0 total-selling-price">{{ $_total_am }}</h4>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
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
                                            <td class="grandTotalSellingQty">{{ $_g_total_qty }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Unit Price</th>
                                            <td class="grandTotalUnitPrice">{{ $_g_total_up }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Sell Commission</th>
                                            <td class="grandTotalCommission">{{ $_g_total_sc }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Amount</th>
                                            <td class="grandTotalSellingPrice">{{ $_g_total_am }}</td>
                                        </tr>
                                    </table>
                                </div>
                            @endif

                            <!-- end row -->

                        </div>

                        <div class="d-print-none">
                            <div class="float-end">
                                <a href="javascript:window.print()"
                                    class="btn btn-success waves-effect waves-light"><i
                                        class="fa fa-print"></i></a>
                                <a href="#"
                                    class="btn btn-primary waves-effect waves-light ms-2">Download</a>
                            </div>
                        </div>

                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->


        </div>
    </div>
@endsection

{{-- 
@section('admin_custom_js')
    <script>
        function priceUpdate() {
            let grandTotalSellingQty = 0;
            let grandTotalUnitPrice = 0;
            let grandTotalCommission = 0;
            let grandTotalSellingPrice = 0;
            $('.report-table').each(function() {
                let totalSellingQty = 0;
                let totalUnitPrice = 0;
                let totalCommission = 0;
                let totalSellingPrice = 0;

                $(this).find('tbody tr').each(function() {
                    let sellingQty = parseInt($(this).find('input[name="selling_qty[]"]').val());
                    let unitPrice = parseInt($(this).find('input[name="unit_price[]"]').val());
                    let commission = parseInt($(this).find('input[name="total_sell_commission[]"]').val());
                    let sellingPrice = (sellingQty * unitPrice) - commission;
                    $(this).find('.row-selling_price').text(sellingPrice);
                    totalSellingQty += sellingQty;
                    totalUnitPrice += unitPrice;
                    totalCommission += commission;
                    totalSellingPrice += sellingPrice;
                });
                grandTotalSellingQty += totalSellingQty;
                grandTotalUnitPrice += totalUnitPrice;
                grandTotalCommission += totalCommission;
                grandTotalSellingPrice += totalSellingPrice;
                $(this).find('.tfoot-row .total-selling-qty').text(totalSellingQty);
                $(this).find('.tfoot-row .total-unit-price').text(totalUnitPrice);
                $(this).find('.tfoot-row .total-commission').text(totalCommission);
                $(this).find('.tfoot-row .total-selling-price').text(totalSellingPrice);
            });
            $(".grandTotalSellingQty").text(grandTotalSellingQty);
            $(".grandTotalUnitPrice").text(grandTotalUnitPrice);
            $(".grandTotalCommission").text(grandTotalCommission);
            $(".grandTotalSellingPrice").text(grandTotalSellingPrice);
        }
    </script>
@endsection --}}

