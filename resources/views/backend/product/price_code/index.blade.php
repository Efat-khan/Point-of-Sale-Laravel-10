@extends('admin.admin_master')
@section('admin')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Product Price Code </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            @can('product-list')
                            <li class="breadcrumb-item m-2 "><a href="{{route('product.all')}}">ALL PRODUCT</a></li>
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
        <!-- end page title -->
        <div class="row">
            @can('product-price-code-create')
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-0">Add Price Code</h4> <br>
                        <form method="POST" action="{{ route('productpricecode.store')}}" id="myForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="form-group col-sm-6">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Number</label>
                                    <input name="number" id="number" class="form-control" type="text" value="">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Code</label>
                                    <input name="code" id="code" class="form-control" type="text" value="">
                                </div>
                                <div class="form-group col-sm-1 mt-3">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-plus-circle"></i> Add </button>
                                </div>
                            </div>
                            <!-- end row -->
                    </div>
                </div>
            </div> <!-- end col -->
            @endcan
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row pb-2">
                            <div class="col-6">
                                <h4 class="card-title">All Product Price Code</h4>
                            </div>
                            <div class="col-6">
                            </div>
                        </div>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20px">#</th>
                                    <th>Number</th>
                                    <th>Symbols</th>
                                    <th width="30px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key => $item)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ $item->number }} </td>
                                    <td> {{ $item->code }} </td>
                                    <td>
                                        @can('product-price-code-edit')
                                        <a href="{{route('productpricecode.edit',$item->id)}}" class="btn btn-info sm" title="Edit Data"> <i class="fas fa-edit"></i> </a>
                                        @endcan
                                        @can('product-price-code-delete')
                                            @if ($item->number != '00' || $item->number != '000')
                                            <a href="{{route('productpricecode.delete',$item->id)}}" class="btn btn-danger sm" title="Delete Data" id="delete"> <i class="fas fa-trash-alt"></i> </a>
                                            @endif
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
</div>
@endsection