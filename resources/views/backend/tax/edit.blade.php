@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Tax</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('tax.index')}}">BACK</a></li>
                            <!-- <li class=""><a href="{{route('product.all')}}" class="btn btn-dark btn-rounded waves-effect waves-light">
                                <i class="fa fa-chevron-circle-left"> Back </i></a></li> -->
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('tax.update')}}" id="update">
            @csrf
            <input name="id" class="form-control" type="text" placeholder="Enter Tax Name" value="{{$data->id}}" hidden>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-0">Edit Tax</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="example-text-input" class="col-sm-2 col-form-label">Tax Name</label>
                            <input name="name" class="form-control" type="text" placeholder="Enter Tax Name" value="{{$data->name}}">
                        </div>
                        <div class="col-sm-6">
                            <label for="example-text-input" class="col-sm-2 col-form-label">Tax Rate(%)</label>
                            <input name="rate" class="form-control" type="text" placeholder="Enter Tax Rate" value="{{$data->rate*100}}">
                        </div>
                        <div class="col-sm-2 mt-3">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Update </button>
                        </div>
                        <!-- end row -->
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Java Script validation for empty form -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#myForm').validate({
            rules: {
                name: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: 'Please Enter the Brand name',
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