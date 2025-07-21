@extends('admin.admin_master')
@section('admin')
<style>
    div.dataTables_wrapper div.dataTables_filter input {
        width: 400px !important;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <!-- Title -->
            <!-- <div class="col-md-3 col-sm-12">
                <div class="page-title-box">
                    <h4 class="mb-0">Filter Customers</h4>
                </div>
            </div> -->
            <!-- Dropdown -->
            <div class="col-md-6 col-sm-12">
                <select id="customer_filter" class="form-select" onchange="updateDashboard()">
                    <option value="">Select Customer</option>
                    @foreach($customers_for_filter as $customer)
                    <option value="{{ $customer->id }}" {{ $customer_id != null ? ($customer_id ==$customer->id? 'selected':''):'' }}>{{ $customer->name }} - {{ $customer->mobile_no }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Buttons -->
            <div class="col-md-6 col-sm-12">
                <div class="d-flex justify-content-end gap-2 mt-2 mt-md-0">
                    <a href="{{ route('customer.all') }}" class="btn btn-outline-dark">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                    <a href="{{ route('customer.all-report-pdf', $customer_id ?? null) }}" class="btn btn-dark waves-effect waves-light">

                        <i class="mdi mdi-printer"></i> Print
                    </a>
                    @can('customer-create')
                    <a href="{{ route('customer.add') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> ADD
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">ALL CUSTOMER</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('customer.add')}}"> ADD CUSTOMER </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('customer.due_payment.all')}}"> CUSTOMER DUE & PAYMENT</a></li>
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
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20px;">#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Office Address</th>
                                    <th>Contact Person</th>
                                    <th width="30px;">Action</th>
                                </tr>
                            </thead>
                            @php
                            function breakIntoLines($text, $wordsPerLine = 5) {
                            $words = explode(' ', $text);
                            $chunks = array_chunk($words, $wordsPerLine);
                            return implode('<br>', array_map(function($chunk) {
                            return implode(' ', $chunk);
                            }, $chunks));
                            }
                            @endphp
                            <tbody>
                                @foreach($customers as $key => $item)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ $item->name }} </td>
                                    <td> {{ $item->mobile_no }}<br> {{ $item->alt_mobile_no?$item->alt_mobile_no:'' }} </td>
                                    <td> {{ $item->email }}<br>{{ $item->alt_email?$item->alt_email:'' }}</td>
                                    <td>
                                        {!! breakIntoLines($item->office_address) !!} <br>
                                        Factory: {!! breakIntoLines($item->factory_address ?? '') !!}
                                    </td>
                                    <td> {{ $item->contact_person_name?'Name: '.$item->contact_person_name:'Not Available' }}<br>{{ $item->contact_person_phone?'Phone: '.$item->contact_person_phone:'Not Available' }} </td>

                                    <td>
                                        @can('customer-edit')
                                        <a href="{{route('customer.edit',$item->id)}}" class="btn btn-info sm" title="Edit Data"> <i class="fas fa-edit"></i> </a>
                                        @endcan
                                        @can('customer-delete')
                                        <a href="{{route('customer.delete',$item->id)}}" class="btn btn-danger sm" title="Delete Data" id="delete"> <i class="fas fa-trash-alt"></i> </a>
                                        @endcan
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
</div> <!-- End Page-content -->

@endsection
@section('admin_custom_js')
<script>
    $(document).ready(function() {
        $('#customer_filter').select2({
            placeholder: "Select Company",
            allowClear: true,
            width: '100%'
        });
    });
</script>
<script>
    function updateDashboard() {
        // Convert dates to a suitable format for your backend
        const customer_filter = document.getElementById('customer_filter').value;
        // alert(invoice_type_filter);
        var url = '{{ url()->current() }}?'
        url += '&customer_filter=' + customer_filter;
        window.location.href = url;
    }
</script>
@endsection