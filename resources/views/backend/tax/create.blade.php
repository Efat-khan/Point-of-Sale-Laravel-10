@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Vat</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            @can('product-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('product.all')}}">ALL PRODUCT</a></li>
                            @endcan
                            @can('product-price-code-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('productpricecode.all')}}">PRODUCT PRICE CODE</a></li>
                            @endcan
                            @can('product-label-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('productLabelsprint.index')}}">PRINT PRODUCT LABELS</a></li>
                            @endcan
                            @can('brand-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('brand.add')}}">ALL BRAND</a></li>
                            @endcan
                            @can('category-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('category.add')}}">ALL CATEGORY</a></li>
                            @endcan
                            @can('unit-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('unit.all')}}">ALL UNIT</a></li>
                            @endcan

                        </ol>
                    </div>
                </div>
            </div>
        </div>
        @can('tax-create')
        <form method="POST" action="{{ route('tax.store')}}" id="create">
            @csrf
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Add VAT</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                            <input name="name" class="form-control" type="text" placeholder="Enter VAT Name">
                        </div>
                        <div class="col-sm-6">
                            <label for="example-text-input" class="col-sm-2 col-form-label">Rate(%)</label>
                            <input name="rate" class="form-control" type="text" placeholder="Enter VAT Rate">
                        </div>
                        <div class="col-sm-2 mt-3">
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus-circle"></i> Add </button>
                        </div>
                        <!-- end row -->
                    </div>
                </div>
            </div>
        </form>
        @endcan
    </div>
    <div class="container-fluid">
        <!-- start page title -->
        <!-- <div class="row"> -->
            <!-- <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    </div>
                </div>
            </div> -->
            <!-- end page title -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                        <h4 class="card-title">All VAT </h4>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20px;">#</th>
                                    <th>Name</th>
                                    <th>Rate</th>
                                    <th width="30px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($data as $key => $item)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ $item->name }} </td>
                                    <td> {{ $item->rate*100 }} % </td>
                                    <td>
                                        @can('tax-edit')
                                        <a href="{{route('tax.edit',$item->id)}}" class="btn btn-info sm" title="Edit Data"> <i class="fas fa-edit"></i> </a>
                                        @endcan
                                        @can('tax-delete')
                                        <a href="{{route('tax.delete',$item->id)}}" class="btn btn-danger sm" title="Delete Data" id="delete"> <i class="fas fa-trash-alt"></i> </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
        <!-- </div> end row -->
    </div>
</div>
<!-- Java Script validation for empty form -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#create').validate({
            rules: {
                name: {
                    required: true,
                },
                rate: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: 'Please Enter the Tax name',
                },
                rate: {
                    required: 'Please Enter the Tax rate',
                },
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>
@endsection