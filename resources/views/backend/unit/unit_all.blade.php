@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Units</h4>
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
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        @can('unit-create')
        <div class="row">
            <form method="POST" action="{{ route('unit.store')}}" id="myForm">
                @csrf
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-0">Add Unit</h4>
                            <div class="row mb-3">
                                <div class="form-group col-sm-12">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Name</label>
                                    <input name="name" class="form-control" type="text">
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">All Unit</h4>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20px;">Sl</th>
                                    <th>Name</th>
                                    <th width="30px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($units as $key => $item)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ $item->name }} </td>
                                    <td>
                                        @can('unit-edit')
                                        <a href="{{route('unit.edit',$item->id)}}" class="btn btn-info sm" title="Edit Data"> <i class="fas fa-edit"></i> </a>
                                        @endcan
                                        @can('unit-delete')
                                        <a href="{{route('unit.delete',$item->id)}}" class="btn btn-danger sm" title="Delete Data" id="delete"> <i class="fas fa-trash-alt"></i> </a>
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