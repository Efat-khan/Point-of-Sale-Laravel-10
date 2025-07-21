@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Supplier Purchase</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 ">SUPPLIER PURCHASE REPORT</a> </li>
                            <li class=" active">
                                <form action="{{route('purchase.wise.due.report')}}" method="GET" id="myForm">
                                    <button value="{{$supplier_purchase_payment->supplier_id}}" name="supplier_id" class="btn btn-dark btn-rounded waves-effect waves-light" style="float:right;" title="Purchase Details">
                                        <i class="fa fa-chevron-circle-left"> Back </i>
                                    </button>
                                </form>
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
                                <div>
                                    <div class="row mb-4">
                                        <div class="p-2 col-6">
                                            <h3 class="font-size-16"><strong>Supplier Purchase ( Purchase No: #{{ $supplier_purchase_payment['supplier_purchese']['purchase_no'] }} ) </strong></h3>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <button onclick="edit_purchase()" id="editbtn" class="btn btn-outline-info">EDIT PURCHASE</button>
                                            <a href="{{ route('purchase.all') }}" class="btn btn-outline-primary"><i class="fas fa-list"></i> ALL PURCHASE</a>
                                            <a href="{{ route('purchase.add') }}" class="btn btn-outline-primary"><i class="fas fa-plus"></i> ADD PURCHASE</a>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td><strong>Supplier Name </strong></td>
                                                        <td class="text-center"><strong>Mobile</strong></td>
                                                        <td class="text-center"><strong>Address</strong>
                                                        </td>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td> {{ $supplier_purchase_payment->supplier_id !=-1 ?$supplier_purchase_payment['supplier']['name']:'N/A' }}</td>
                                                        <td class="text-center">{{ $supplier_purchase_payment->supplier_id !=-1 ?$supplier_purchase_payment['supplier']['mobile_no'] :'N/A' }}</td>
                                                        <td class="text-center">{{ $supplier_purchase_payment->supplier_id !=-1 ?$supplier_purchase_payment['supplier']['address']:'N/A'  }}</td>

                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div> <!-- end row -->



                        <div class="row">
                            <div class="col-12">
                                <form method="post" action="{{ route('purchase.supplier.update',$supplier_purchase_payment->purchase_id)}}">

                                    @csrf

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <td><strong>Sl </strong></td>
                                                    <td class="text-center"><strong>Brand</strong></td>
                                                    <td class="text-center"><strong>Category</strong></td>
                                                    <td class="text-center"><strong>Product Name</strong>
                                                    </td>
                                                    <td class="text-center"><strong>Current Stock</strong>
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
                                                $supplier_purchese_details = App\Models\SupplierPurcheseDetails::where('purchase_id',$supplier_purchase_payment->purchase_id)->get();
                                                @endphp
                                                @foreach($supplier_purchese_details as $key => $details)
                                                <tr>
                                                    <td class="text-center">{{ $key+1 }}</td>
                                                    <td class="text-center">{{ (!empty($details['brand']['name'])?$details['brand']['name']:'Null') }}</td>
                                                    <td class="text-center">{{ (!empty($details['category']['name'])?$details['category']['name']:'Null') }}</td>
                                                    <td class="text-center">{{ $details['product']['name'] }}</td>
                                                    <td class="text-center">{{ number_format($details['product']['quantity']) }}</td>
                                                    <td class="text-center">{{ number_format($details->buying_qty) }}</td>
                                                    <td class="text-center">৳ {{ number_format($details->product_buying_price,2) }} Tk</td>
                                                    <td class="text-center">৳ {{ number_format($details->product_buying_price*$details->buying_qty,2) }} Tk</td>
                                                    <td class="text-center">৳ {{ number_format($details->total_db_com + $details->total_mc_com + $details->total_sp_com,2)}} Tk</td>
                                                    <td class="text-end">৳ {{ number_format($details->buying_price,2) }} Tk</td>

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
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line"></td>
                                                    <td class="thick-line text-center">
                                                        <strong>Subtotal</strong>
                                                    </td>
                                                    <td class="thick-line text-end">৳ {{ number_format($total_sum,2) }} Tk</td>
                                                </tr>
                                                <tr>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line text-center">
                                                        <strong>Discount Amount</strong>
                                                    </td>
                                                    <td class="no-line text-end">৳ {{ number_format($supplier_purchase_payment->discount_amount,2) }} Tk</td>
                                                </tr>
                                                <tr>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line text-center">
                                                        <strong>Total Payable</strong>
                                                    </td>
                                                    <td class="no-line text-end">
                                                        <h5 class="m-0">৳ {{ number_format($supplier_purchase_payment->total_amount,2) }} Tk</h5>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line text-center">
                                                        <strong>Paid Amount</strong>
                                                    </td>
                                                    <td class="no-line text-end">৳ {{ number_format($supplier_purchase_payment->paid_amount,2) }} Tk</td>
                                                </tr>

                                                <tr>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line"></td>
                                                    <td class="no-line text-center">
                                                        <strong>Due Amount</strong>
                                                    </td>
                                                    <input type="hidden" name="due_amount" value="{{$supplier_purchase_payment->due_amount }}">
                                                    <td class="no-line text-end">
                                                        <h4 class="m-0 text-danger">৳ {{ number_format($supplier_purchase_payment->due_amount,2) }} Tk</h4>
                                                    </td>
                                                </tr>

                                                
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- ############## supplier_purchase_payment update select button ############## -->

                                    <!-- <div class="row">

                                        <div class="form-group col-md-3">
                                            <label> Paid Status </label>
                                            <select name="paid_status" id="paid_status" class="form-select">
                                                <option value="">Select Status </option>
                                                <option value="full-paid">Full Paid </option>
                                                <option value="partial-paid">Partial Paid </option>
                                            </select>
                                            <br>
                                            <input type="text" name="paid_amount" class="form-control paid_amount" id="paid_amount" placeholder="Enter Paid Amount" style="display:none;">
                                        </div>


                                        <div class="form-group col-md-3">
                                            <div class="md-3">
                                                <label for="example-text-input" class="form-label">Date</label>
                                                <input class="form-control example-date-input" placeholder="YYYY-MM-DD" name="date" type="date" id="date" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="bank_name" class="form-label">Bank</label>
                                            <select id="disabledSelect" class="form-select" name="bank_name">
                                                <option value="Hand Cash">Hand Cash</option>
                                                <option value="National Bank">National Bank</option>
                                                <option value="Islami Bank">Islami Bank</option>
                                                <option value="Eastern Bank">Eastern Bank</option>

                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="account_no" class="form-label">Account No.</label>
                                            <input type="text" name="account_no" min="1" class="form-control" id="account_no">
                                        </div>


                                        <div class="form-group col-md-12">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <div class="md-3" style="padding-top: 30px;">
                                                <button type="submit" class="btn btn-info">purchase Update</button>
                                            </div>

                                        </div>

                                    </div> -->
                            </div> <!-- end row -->

                            <!-- ############## End supplier_purchase_payment update select button ############## -->



                        </div>



                    </div>

                    </form>

                </div>

            </div> <!-- end row -->

        </div>

    </div>
</div> <!-- end col -->
</div> <!-- end row -->

</div> <!-- container-fluid -->


<!-- MODEL -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Purchase Edit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row mb-3">
                    <label for="example-text-input" class="col-md-3 col-form-label">Purchase No. :</label>
                    <div class="col-md-3 ">
                        <input class="form-control text-start" type="text" name="purchase_no" value="{{  $supplier_purchase->purchase_no }}" id="purchase_no" readonly style="background-color:#ddd; margin-left: -180px; ">
                    </div>
                    <label for="example-text-input" class="col-md-3 col-form-label" style="text-align: right;">Date:</label>
                    <div class="col-md-3">
                        <input class="form-control example-date-input" name="date" value="{{ date('Y-m-d') }}" type="date" id="date">
                    </div>

                </div>
                <!-- end first row purchase NO -->

                <div class="row">
                    <div class="col-md-2">
                        <div class="md-3">
                            <label for="example-text-input" class="form-label">Brand</label>
                            <select name="brand_id" id="brand_id" class="form-select " aria-label="Default select example">
                                <option selected="">Select brand</option>
                                @foreach(\App\Models\Brand::get() as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="md-3">
                            <label for="example-text-input" class="form-label">Category</label>
                            <select name="category_id" id="category_id" class="form-select " aria-label="Default select example">
                                <option selected="">Select Category</option>

                            </select>
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="md-3">
                            <label for="example-text-input" class="form-label">Product</label>
                            <select name="product_id" id="product_id" class="form-select " aria-label="Default select example">
                                <option selected="">Select Product</option>

                            </select>
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="md-3">
                            <label for="example-text-input" class="form-label">Stock(Pcs/Kg)</label>
                            <input class="form-control example-date-input" name="current_stock_qty" type="text" id="current_stock_qty" value="0" readonly style="background-color:#ddd">
                        </div>
                    </div>


                    <div class="col-md-2">
                        <div class="md-3">
                            <label for="example-text-input" class="form-label" style="margin-top:43px;"> </label>

                            <i class="btn btn-secondary btn-rounded waves-effect waves-light fas fa-plus-circle addeventmore"> Add More</i>
                        </div>
                    </div>

                </div>
                <!-- OLD EDIT purchase -->
                <div class="card-body">
                    <form method="post" action="{{ route('purchase.update',$supplier_purchase_payment['supplier_purchese']['id']) }}" id="update_purchase">
                        @csrf
                        <input class="form-control" type="number" name="purchase_no" value="{{ $supplier_purchase->purchase_no }}" id="purchase_no" hidden>
                        <input type="hidden" id="supplier_purchese_payment_id" name="supplier_purchese_payment_id" value="">
                        <input type="hidden" id="supplier_id" name="supplier_id" value="">

                        <table class="table-sm table-bordered" width="100%" style="border-color: #ddd;">
                            <thead>
                                <tr>
                                    <th colspan="1" style="width: 20px;">Product Name </th>
                                    <th colspan="1">PSC/Stock</th>
                                    <th colspan="2">Unit Price </th>
                                    <th colspan="1">DB Com</th>
                                    <th colspan="1">MC Com</th>
                                    <th colspan="1">SP Com</th>
                                    <th colspan="5">Total Price</th>

                                </tr>
                            </thead>

                            <tbody id="addRow" class="addRow">

                            </tbody>

                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-end">
                                        <div class="form-group">
                                            <select name="discount_status" id="discount_status" class="form-select" style="float: right; width:31%">
                                                <option value="fixed_discount">Fixed Discount</option>
                                                <option value="percentage_discount">Percentage Discount(%)</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td colspan="5">
                                        <input type="text" name="discount_amount" id="discount_amount" class="form-control discount_amount" value="0" autocomplete="off">
                                    </td>

                                <tr>
                                    <!-- <td colspan="1" class="text-start">Total Buying Price Code</td>
                                        <td colspan="2">
                                            <input type="text" name="secret_grand_total_price_code" value="0" id="secret_grand_total_price_code" class="form-control secret_grand_total_price_code" readonly style="background-color: #ddd;">
                                        </td> -->

                                    <td colspan="7" class="text-end">Discount</td>
                                    <td colspan="5">
                                        <input type="text" name="discount_show" id="discount_show" class="form-control discount_show" value="0" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-end">
                                        Total Discount
                                    </td>
                                    <td colspan="5">
                                        <input type="text" name="total_discount_amount" id="total_discount_amount" class="form-control total_discount_amount" value="0" readonly>
                                    </td>

                                </tr>
                                <tr>
                                    <!-- <td colspan="1" class="text-start">Total Buying Price Code</td>
                                        <td colspan="2">
                                            <input type="text" name="secret_grand_total_price_code" value="0" id="secret_grand_total_price_code" class="form-control secret_grand_total_price_code" readonly style="background-color: #ddd;">
                                        </td> -->

                                    <td colspan="7" class="text-end">Grand Total</td>
                                    <td colspan="5">
                                        <input type="text" name="estimated_amount" value="0" id="estimated_amount" class="form-control estimated_amount" readonly style="background-color: #ddd;">
                                    </td>
                                </tr>

                                <tr>
                                   
                                    <td colspan="7" class="text-end">
                                        <div class="form-group">
                                            <select name="paid_status" id="paid_status" class="form-select" style="float: right; width:38%">
                                                <option value="full-due">Select Paid Status</option>
                                                <option value="full-due">Full Due</option>
                                                <option value="full-paid">Full Paid</option>
                                                <option value="partial-paid">Partial Paid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td colspan="5">
                                        <input type="text" name="paid_amount" class="form-control paid_amount" id="paid_amount" readonly  value="0" autocomplete="off">
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="7" class="text-end">Due</td>
                                    <td colspan="5">
                                        <input type="text" name="due_amount" class="form-control due_amount" id="due_amount" style="background-color: #e7b5b5;" value="0" readonly>
                                    </td>
                                </tr>

                            </tbody>
                        </table><br>

                        <div class="row">

                            <!-- Customer Data Load -->
                            <div class="form-group col-md-4">
                                <label> Select Supplier </label>
                                <select name="supplier_id" id="supplier_id" class="form-select" required disabled>
                                    <option value="-1">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $supplier_purchase_payment->supplier_id == $supplier->id?'selected':'' }}>{{$supplier->name }} - {{$supplier->mobile_no }}</option>
                                    @endforeach
                                    <option value="0">+ New Supplier</option>
                                </select><br>
                            </div>
                            <!-- End of Customer Data Load -->
                            <div class="modal-footer col-md-8 pt-4">
                                <button type="button" class="btn btn-success " onclick="$('#update_purchase').submit()"><i class="fas fa-check-circle"></i> Update</button>
                            </div>

                        </div> <!-- End Row -->


                        <!-- Hide New Supplier insert form -->
                        <div class="row new_supplier" style="display: none;">
                            <div class="form-group col-md-4">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                            </div>

                            <div class="form-group col-md-4">
                                <input type="number" name="mobile_no" id="mobile_no" class="form-control" placeholder="Mobile No">
                            </div>

                            <div class="form-group col-md-4">
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                            </div>
                            <div class="form-group col-md-12 pt-2">
                                <textarea name="address" id="address" class="form-control" placeholder="Write Address Here..."></textarea>
                            </div>

                        </div> <br>
                        <!-- End Hide New Customer insert form -->

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <textarea name="description" id="description" class="form-control" placeholder="Write Description Here..."></textarea>
                            </div>
                        </div> <br>

                    </form>

                </div>
                <!-- OLD EDIT PURCHASE -->

            </div>
        </div>
    </div>
</div>
</div>


<script id="document-template" type="text/x-handlebars-template">

    <tr class="delete_add_more_item" id="delete_add_more_item">
        <input type="hidden" name="date" value="@{{date}}">
        <input type="hidden" name="purchase_no" value="@{{purchase_no}}">
        <input type="hidden" name="purchase_details_id[]" value="@{{purchase_details_id}}">
        <input type="hidden" name="category_id[]" value="@{{category_id}}">
        <input type="hidden" name="brand_id[]" value="@{{brand_id}}">
        <input type="hidden" name="product_id[]" value="@{{product_id}}">

        <td colspan="1">
            @{{ product_name }}
        </td>
            
            <td colspan="1">
                <input type="number"  min="1" class="form-control buying_qty text-right" name="buying_qty[]" id="buying_qty" value="@{{ buying_qty }}"> 
                <input type="text" class="form-control stock_quantity text-right" name="stock_quantity[]" value="@{{product_quantity}}" readonly> 
        </td>
    
        <td colspan="2">
            <input type="text" class="form-control product_buying_price text-right" name="product_buying_price[]" value="@{{product_buying_price}}"> 
        </td>


        <!--db_com Discount  -->
        <td colspan="1">
                <div class="form-check form-switch">
                    <input class="form-check-input db_com_checked" type="checkbox" @{{ db_com_checked }}>
                    <label class="form-check-label db_com_checked_label" for="flexSwitchCheckDefault">@{{db_com_checked_label}}</label>
                </div>
                <input type="text" class="form-control db_com text-right" id="db_com" name="db_com[]" value="@{{db_com}}" autocomplete="off">
                <input type="text" class="form-control total_db_com text-right" id="total_db_com" name="total_db_com[]" value="@{{total_db_com}}" readonly >
        </td>
        <!--mc_com  Discount  -->
        <td colspan="1">
                <div class="form-check form-switch">
                    <input class="form-check-input mc_com_checked" type="checkbox" @{{ mc_com_checked }}>
                    <label class="form-check-label mc_com_checked_label" for="flexSwitchCheckDefault">@{{mc_com_checked_label}}</label>
                </div>
                <input type="text" class="form-control mc_com text-right" id="mc_com" name="mc_com[]" value="@{{mc_com}}" autocomplete="off">
                <input type="text" class="form-control total_mc_com text-right" id="total_mc_com" name="total_mc_com[]" value="@{{total_mc_com}}" readonly >
        </td>
        <!-- sp_com Discount  -->
        <td colspan="1">
                <div class="form-check form-switch">
                    <input class="form-check-input sp_com_checked" type="checkbox" @{{ sp_com_checked }}>
                    <label class="form-check-label sp_com_checked_label" for="flexSwitchCheckDefault">@{{sp_com_checked_label}}</label>
                </div>
                <input type="text" class="form-control sp_com text-right" id="sp_com" name="sp_com[]" value="@{{sp_com}}" autocomplete="off">
                <input type="text" class="form-control total_sp_com text-right" id="total_sp_com" name="total_sp_com[]" value="@{{total_sp_com}}" readonly >
        </td>

        <td colspan="5">
            <input type="text" class="form-control buying_price text-right" id="buying_price" name="buying_price[]" value="@{{buying_price}}" readonly> 
        </td>

        <td colspan="1">
            <i class="btn btn-danger btn-sm fas fa-window-close removeeventmore"></i>
        </td>
    </tr>
</script>
    <script>
        $('.select2').select2({
            dropdownParent: $('#staticBackdrop')
        });
    </script>

<!-- ALL JS CODE -->
<script type="text/javascript">
// GENERATE ALL PURCHES PRODUCT START//
function edit_purchase() {

$.ajax({
    type: 'GET',
    url: "{{ route('purchase.data', $supplier_purchase->id) }}",
    success: function(data) {
        console.log(data);
        if (data) {
            var html = '';
            for (let i of data['supplier_purchese_details']) {
                var source = $("#document-template").html();
                var tamplate = Handlebars.compile(source);
                var _data = {
                    date: i.date,
                    purchase_no: {{$supplier_purchase -> id}},
                    purchase_details_id: i.id,
                    brand_id: i.brand_id,
                    category_id: i.category_id,
                    product_id: i.product_id,
                    product_name: i.product.name,
                    buying_qty: i.buying_qty,
                    product_quantity: i.product.quantity,
                    product_buying_price: i.product_buying_price,

                    db_com: i.db_com,
                    total_db_com: i.total_db_com,
                    db_com_checked: (i.db_com != i.total_db_com ? 'checked' : ''),
                    db_com_checked_label: (i.db_com != i.total_db_com ? '% Percent' : 'Fixed'),

                    mc_com: i.mc_com,
                    total_mc_com: i.total_mc_com,
                    mc_com_checked: (i.mc_com != i.total_mc_com ? 'checked' : ''),
                    mc_com_checked_label: (i.mc_com != i.total_mc_com ? '% Percent' : 'Fixed'),

                    sp_com: i.sp_com,
                    total_sp_com: i.total_sp_com,
                    sp_com_checked: (i.sp_com != i.total_sp_com ? 'checked' : ''),
                    sp_com_checked_label: (i.sp_com != i.total_sp_com ? '% Percent' : 'Fixed'),

                    buying_price: i.buying_price,
                };
                html += tamplate(_data);

            }
            $("#addRow").html(html);
            
            $("#discount_amount").val(data.supplier_purchese_payment.discount_amount);
            $("#estimated_amount").val(data.supplier_purchese_payment.total_amount);
            $("#paid_status").val(data.supplier_purchese_payment.paid_status);
            $("#paid_amount").val(data.supplier_purchese_payment.paid_amount);
            $("#due_amount").val(data.supplier_purchese_payment.due_amount);
            $("#supplier_purchese_payment_id").val(data.supplier_purchese_payment.id);
            $("#supplier_id").val(data.supplier_purchese_payment.supplier_id);
            $("#staticBackdrop").modal('show');
            $("#discount_amount").trigger('')
            // $('.sell_commission').click();
        }

    }
});
}
    // GENERATE ALL PURCHES PRODUCT END//
        var product_list = [];
        $(document).ready(function(){
            $(document).on("click",".addeventmore", function(){
                var date = $('#date').val();
                var purchase_no = $('#purchase_no').val();
                // var supplier_id = $('#supplier_id').val();
                var brand_id = $('#brand_id').val();
                // var brand_name = $('#brand_id').find('option:selected').text();
                var category_id = $('#category_id').val();
                // var category_name = $('#category_id').find('option:selected').text();
                var product_id = $('#product_id').val();
                var product_name = $('#product_id').find('option:selected').text();
                let product_price = 0;
                let db_com = 0;
                let m_com = 0;
                let s_com = 0;
                if(product_list.length > 0 && product_id > 0){
                    let product = product_list.find(product => product.id == product_id);
                    
                    product_buying_price = product.product_buying_price;
                    product_quantity = product.quantity;
                    product_discount = product.product_discount;
                    
                }
                if (date == ''){
                    $.notify("Date is Required",{
                        globalPosition: 'top right',
                        className:'error'
                    });
                    return false;
                }
                if(category_id == ''){
                    $.notify("Category is Required",{
                        globalPosition: 'top right',
                        className:'error'
                    });
                    return false;
                }
                if(product_id == ''){
                    $.notify("Product Field is Required",{
                        globalPosition: 'top right',
                        className:'error'
                    });
                    return false;
                }
                var source = $("#document-template").html();
                var tamplate = Handlebars.compile(source);
                var data = {
                    date:date,
                    purchase_no:purchase_no,
                    // supplier_id:supplier_id,
                    category_id:category_id,
                    brand_id:brand_id,
                    // category_name:category_name,
                    product_id:product_id,
                    product_name:product_name,
                    product_buying_price:product_buying_price,
                    buying_qty:'0',
                    product_quantity:product_quantity,
                    sell_commission:product_discount,
                    db_com:product_discount,
                    mc_com:product_discount,
                    sp_com:product_discount,
                    db_com_checked_label:'Fixed',
                    mc_com_checked_label:'Fixed',
                    sp_com_checked_label:'Fixed',
                    buying_price:'0',
                };
                var html = tamplate(data);
                $("#addRow").append(html);
                totalAmountPrice();
            });
            $(document).on("click",".removeeventmore",function(event){
                $(this).closest(".delete_add_more_item").remove();
                totalAmountPrice();
            });
            
            

            // CALCULATION
            $(document).on('input', 'input.product_buying_price, input.buying_qty, input.db_com, input.mc_com, input.sp_com,.db_com_checked,.sp_com_checked,.mc_com_checked', function() {
                var $row = $(this).closest("tr");

                // Parse values to floats and default to 0 if not a number
                var product_buying_price = parseFloat($row.find("input.product_buying_price").val()) || 0;
                var buying_qty = parseFloat($row.find("input.buying_qty").val()) || 0;
                
                // Calculate total commissions
                
                
                //---------Calculate total commissions-----------//
                var db_com = parseFloat($row.find("input.db_com").val()) || 0; //Extra: db_com
                if (!isNaN(db_com) && db_com.length != 0) {
                    var total_db_com = 0;
                    var isPercant = $row.find('.db_com_checked').is(':checked');
                    var label = $row.find('.db_com_checked_label');
                
                    if (isPercant) {
                        // If % Percent is selected
                        label.text('% Percent');
                        var baseValue = product_buying_price * buying_qty; // Assuming a base value for calculation
                        var total_db_com = (db_com / 100) * baseValue; // Calculate percentage
                    } else {
                        // If Fixed is selected
                        label.text('Fixed');
                        total_db_com = db_com;
                    }
                    $row.find("input.total_db_com").val(total_db_com.toFixed(2));
                }

                var mc_com = parseFloat($row.find("input.mc_com").val()) || 0; //Extra: mc_com
                if (!isNaN(mc_com) && mc_com.length != 0) {
                    var total_mc_com = 0;
                    var isPercant = $row.find('.mc_com_checked').is(':checked');
                    var label = $row.find('.mc_com_checked_label');
                
                    if (isPercant) {
                        // If % Percent is selected
                        label.text('% Percent');
                        var baseValue = product_buying_price * buying_qty; // Assuming a base value for calculation
                        var total_mc_com = (mc_com / 100) * baseValue; // Calculate percentage
                    } else {
                        // If Fixed is selected
                        label.text('Fixed');
                        total_mc_com = mc_com;
                    }
                    $row.find("input.total_mc_com").val(total_mc_com.toFixed(2));
                }

                var sp_com = parseFloat($row.find("input.sp_com").val()) || 0; //Extra: sp_com
                if (!isNaN(sp_com) && sp_com.length != 0) {
                    var total_sp_com = 0;
                    var isPercant = $row.find('.sp_com_checked').is(':checked');
                    var label = $row.find('.sp_com_checked_label');
                
                    if (isPercant) {
                        // If % Percent is selected
                        label.text('% Percent');
                        var baseValue = product_buying_price * buying_qty; // Assuming a base value for calculation
                        var total_sp_com = (sp_com / 100) * baseValue; // Calculate percentage
                    } else {
                        // If Fixed is selected
                        label.text('Fixed');
                        total_sp_com = sp_com;
                    }
                    $row.find("input.total_sp_com").val(total_sp_com.toFixed(2));
                }
                
                //---------discount calculation per product-----------//


                var total_com = total_db_com + total_mc_com + total_sp_com;

                // Calculate total selling price
                var total = (product_buying_price * buying_qty) - total_com;

                // Update input fields with calculated values
                $row.find("input.buying_price").val(total.toFixed(2));
                // $(this).closest("tr").find("input.sell_commission_qty").val(qty);
                $('#total_db_com').trigger('keyup');
                $('#total_mc_com').trigger('keyup');
                $('#total_sp_com').trigger('keyup');
                $('#db_com').trigger('keyup');
                $('#mc_com').trigger('keyup');
                $('#sp_com').trigger('keyup');
                $('#paid_amount').trigger('keyup');
                $('#paid_status').trigger('keyup');
                $('#discount_status').trigger('keyup');
            });


            $(document).on('keyup', '#discount_amount,#paid_amount,#due_amount,#paid_status,.buying_qty,#discount_status,input.product_buying_price, input.buying_qty, input.db_com, input.mc_com, input.sp_com,.db_com_checked,.sp_com_checked,.mc_com_checked', function() {
                totalAmountPrice();
            });
            // Paid status like full-paid, half paid etc. update section
            $(document).on('change', '#paid_status', function() {
                if ($(this).val() == 'full-due') {
                    $('#paid_amount').val(0);
                    $('#paid_amount').attr('readonly', true);
                } else if ($(this).val() == 'full-paid') {
                    $('#paid_amount').val($('#estimated_amount').val());
                    $('#paid_amount').attr('readonly', true);
                } else {
                    $('#paid_amount').attr('readonly', false);
                }
                totalAmountPrice();
            })
            //Discount status
            $(document).on('change', '#discount_status', function() {
                totalAmountPrice();
            });
            // Calculate sum of amout in invoice 
            function totalAmountPrice() {
                // total commission individual product
                var total_discount_sum = 0;
                var total_discount_com_sum = 0;
                // total commission of sell_com of individual product
                var total_sp_com_sum = 0;
                $(".total_sp_com").each(function() { //here sell_commision row
                    var value = $(this).val();
                    if (!isNaN(value) && value.length != 0) {
                        total_sp_com_sum += parseFloat(value);
                    }
                });
                // total commission of mc_com of individual product
                var total_mc_com_sum = 0;
                $(".total_mc_com").each(function() { //here sell_commision row
                    var value = $(this).val();
                    if (!isNaN(value) && value.length != 0) {
                        total_mc_com_sum += parseFloat(value);
                    }
                });
                // total commission of db_com of individual product
                var total_db_com_sum = 0;
                $(".total_db_com").each(function() { //here sell_commision row
                    var value = $(this).val();
                    if (!isNaN(value) && value.length != 0) {
                        total_db_com_sum += parseFloat(value);
                    }
                });
                total_discount_com_sum = total_sp_com_sum + total_db_com_sum + total_mc_com_sum;

                // Grand total calculation
                var sum = 0;
                $(".buying_price").each(function() {
                    var value = $(this).val();
                    if (!isNaN(value) && value.length != 0) {
                        sum += parseFloat(value);
                    }
                });
                // Total buying price Calculation
                var product_buying_price_sum = 0;
                $(".buying_price").each(function() {
                    var value = $(this).val();
                    if (!isNaN(value) && value.length != 0) {
                        product_buying_price_sum += parseFloat(value);
                    }
                });

                // Discount calculation
                var discount_status = $('#discount_status').val();
                var discount_amount = parseFloat($('#discount_amount').val());
                if (!isNaN(discount_amount) && discount_amount.length != 0) {

                    if (discount_status == 'percentage_discount') {

                        var discount = (discount_amount / 100) * sum;
                        sum -= parseFloat(discount);
                        total_discount_sum =discount + total_discount_com_sum;
                        $('#discount_show').val(discount.toFixed(2));

                    } else {
                        var discount = (discount_amount * 100) / sum;
                        sum -= parseFloat(discount_amount);
                        total_discount_sum =discount_amount + total_discount_com_sum;
                        $('#discount_show').val(discount.toFixed(2) + ' %');
                    }
                }

                $('#total_discount_amount').val(total_discount_sum.toFixed(2));
                $('#secret_grand_total_price_code').val(product_buying_price_sum);
                $('#estimated_amount').val(sum.toFixed(2));
                if ((sum - product_buying_price_sum) > 0) {
                    $('#total_profit_code').val((sum - product_buying_price_sum));
                } else {
                    $('#total_profit_code').val(-1);
                }

                // Paid calculation
                var paid_amount = parseFloat($('#paid_amount').val());
                if ($('#paid_status').val() == 'full-due') {
                    paid_amount = 0;
                    $('#paid_amount').val(paid_amount);
                } else if ($('#paid_status').val() == 'full-paid') {
                    paid_amount = $('#estimated_amount').val();
                    $('#paid_amount').val(paid_amount);
                }
                if (!isNaN(paid_amount) && paid_amount.length != 0) {
                    sum -= parseFloat(paid_amount);
                }
                $('#due_amount').val(sum.toFixed(2));
                total_buying_price_code_generator('secret_grand_total_price_code');
                total_buying_price_code_generator('total_profit_code');
            }
            // NEW END
           

        });
</script>


<!-- Display Product wise stock  -->
<script type="text/javascript">
    $(function() {
        $(document).on('change', '#product_id', function() {
            var product_id = $(this).val();
            $.ajax({
                url: "{{ route('check-product-stock') }}",
                type: "GET",
                data: {
                    product_id: product_id
                },
                success: function(data) {
                    $('#current_stock_qty').val(data);
                }
            });
        });
    });
</script>
<!-- Loading Category as from Brand -->
<script type="text/javascript">
    $(function() {
        $(document).on('change', '#brand_id', function() {
            var brand_id = $(this).val();
            $.ajax({
                url: "{{ route('get-category-by-brand') }}",
                type: "GET",
                data: {
                    brand_id: brand_id
                },
                success: function(data) {
                    product_list = data;
                    var html = '<option value="">Select Category</option>';
                    $.each(data, function(key, v) {
                        html += '<option value=" ' + v.id + ' "> ' + v.name + '</option>';
                    });
                    $('#category_id').html(html);
                }
            })
        });
    });
</script>
<!-- Loading Product as from Category -->
<script type="text/javascript">
    $(function() {
        $(document).on('change', '#category_id', function() {
            var category_id = $(this).val();
            $.ajax({
                url: "{{ route('get-product') }}",
                type: "GET",
                data: {
                    category_id: category_id
                },
                success: function(data) {
                    product_list = data;
                    var html = '<option value="">Select Product</option>';
                    $.each(data, function(key, v) {
                        html += '<option value=" ' + v.id + ' "> ' + v.name + '</option>';
                    });
                    $('#product_id').html(html);
                }
            })
        });
    });
</script>
<!-- Convert of buying price to price code -->
<script type="text/javascript">
    function total_buying_price_code_generator(id) {
        var buyingPrice = parseInt($('#' + id).val());
        var html = "0";
        var numberDictonary = @json($productPriceCode -> pluck('code', 'number'));
        // console.log(numberDictonary);

        if (buyingPrice != '') {
            html = numberToCode(buyingPrice);
        }
        if (buyingPrice == -1) {
            html = "LOSS";
        }

        function numberToCode(num) {
            var strNum = num.toString(),
                arrStrNum = strNum.split(''), //conver string to array
                reverseArrStrNum = arrStrNum.reverse(), //reverse array
                lastIndex = reverseArrStrNum.length - 1,
                result = "";
            for (var i = lastIndex; i >= 0; i--) {
                result += numberDictonary[parseInt(reverseArrStrNum[i])];
            }
            return result.toUpperCase();
        }
        $("#" + id).val(html);
    }
</script>




@endsection