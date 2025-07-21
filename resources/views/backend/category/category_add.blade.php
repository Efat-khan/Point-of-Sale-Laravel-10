@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Category</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            @can('product-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('product.add')}}">ALL PRODUCT</a></li>
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
                            @can('unit-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('unit.all')}}">ALL UNIT</a></li>
                            @endcan
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        @can('category-create')
        <div class="row">
            <form method="POST" action="{{ route('category.store')}}" id="myForm">
                @csrf
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-0">Add Category</h4>
                            <!-- Brand Dropdown button  -->
                            <div class="row mb-3">
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-12 col-form-label">Brand Name</label>
                                    <select class="form-select" name="brand_id" id="brand_id" aria-label="Default select example">
                                        <option selected="">Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                        @endforeach
                                        <option value="-1">+ Add Brand</option>

                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Category Name</label>
                                    <input name="name" class="form-control" type="text">
                                </div>
                            </div>
                            <!-- Hide New Brand insert form -->
                            <div class="row new_brand mb-3" style="display: none;">
                                <div class="form-group col-md-12">
                                    <input type="text" name="brand_name" class="form-control" placeholder="New Brand Name" required>
                                </div>
                            </div>
                            <!-- end row -->
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus-circle"></i> Add </button>
                        </div>
                    </div>
                </div> <!-- end col -->
            </form>
        </div>
        @endcan
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">ALL Categories</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <!-- <li class="m-2 breadcrumb-item"><a href="{{route('category.add')}}">ADD CATEGORY</a></li> -->
                            <!-- <li class=""><a href="{{route('product.all')}}" class="btn btn-dark btn-rounded waves-effect waves-light">
                                <i class="fa fa-chevron-circle-left"> Back </i></a></li> -->
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Unit All Data </h4>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20px;">#</th>
                                    <th>Brand Name</th>
                                    <th>Category Name</th>
                                    <th width="30px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $key => $item)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ !empty($item['brand']->name)?$item['brand']->name:'N/A'}} </td>
                                    <td> {{ $item->name }} </td>
                                    <td>
                                        @can('category-edit')
                                        <a href="{{route('category.edit',$item->id)}}" class="btn btn-info sm" title="Edit Data"> <i class="fas fa-edit"></i> </a>
                                        @endcan
                                        @can('category-delete')
                                        <a href="{{route('category.delete',$item->id)}}" class="btn btn-danger sm" title="Delete Data" id="delete"> <i class="fas fa-trash-alt"></i> </a>
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
    </div>
</div>

<!-- Java Script validation for empty form -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#myForm').validate({
            rules: {
                brand_id: {
                    required: true,
                },

                name: {
                    required: true,
                },

            },
            messages: {
                brand_id: {
                    required: 'Please select brand name',
                },
                name: {
                    required: 'Please Enter the category name',
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

<script>
    $(document).on('change', '#brand_id', function() {
        // alert();
        var brand_id = $(this).val();
        if (brand_id == -1) {
            $('.new_brand').show();
        } else {
            $('.new_brand').hide();
        }
    });
</script>


@endsection