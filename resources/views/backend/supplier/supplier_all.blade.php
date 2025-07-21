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
                <select id="supplier_filter" class="form-select" onchange="updateDashboard()">
                    <option value="">Select Supplier</option>
                    @foreach($suppliers_for_filter as $supplier)
                    <option value="{{ $supplier->id }}" {{ $supplier_id != null ? ($supplier_id ==$supplier->id? 'selected':''):'' }}>{{ $supplier->name }} - {{ $supplier->mobile_no }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-md-6 col-sm-12">
                <div class="d-flex justify-content-end gap-2 mt-2 mt-md-0">
                    <a href="{{ route('supplier.all') }}" class="btn btn-outline-dark">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                    <a href="{{ route('supplier.all-report-pdf', $supplier_id ?? null) }}" class="btn btn-dark waves-effect waves-light">
                        <i class="mdi mdi-printer"></i> Print
                    </a>
                    @can('supplier-create')
                    <a href="{{ route('supplier.add') }}" class="btn btn-success">
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
                    <h4 class="mb-sm-0">ALL SUPPLIER</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            @can('supplier-create')
                            <li class="m-2 breadcrumb-item"><a href="{{route('supplier.add')}}"> ADD SUPPLIER </a></li>
                            @endcan
                            @can('supplier-transaction-list')
                            <li class="m-2 breadcrumb-item"><a href="{{route('purchase.supplier_wise_purchese_payment.all')}}"> SUPPLIER DUE & PAYMENT</a></li>
                            @endcan
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
                                @foreach($suppliers as $key => $item)
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
                                        @can('supplier-edit')
                                        <a href="{{route('supplier.edit',$item->id)}}" class="btn btn-info sm" title="Edit Data"> <i class="fas fa-edit"></i> </a>
                                        @endcan
                                        @can('supplier-delete')
                                        <a href="{{route('supplier.delete',$item->id)}}" class="btn btn-danger sm" title="Delete Data" id="delete"> <i class="fas fa-trash-alt"></i> </a>
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
        $('#supplier_filter').select2({
            placeholder: "Select Company",
            allowClear: true,
            width: '100%'
        });
    });
</script>
<script>
    function updateDashboard() {
        // Convert dates to a suitable format for your backend
        const supplier_filter = document.getElementById('supplier_filter').value;
        // alert(invoice_type_filter);
        var url = '{{ url()->current() }}?'
        url += '&supplier_filter=' + supplier_filter;
        window.location.href = url;
    }
</script>
@endsection