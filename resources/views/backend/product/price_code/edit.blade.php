@extends('admin.admin_master')
@section('admin')

<div class="page-content">
    <div class="container-fluid">
    <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Product Price Code</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="breadcrumb-item m-2 "><a href="{{route('productpricecode.all')}}">BACK</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-0">Edit Price Code</h4> <br>
                        <form method="POST" action="{{ route('productpricecode.update')}}" id="myForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="form-group col-sm-6">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Number</label>
                                    <input name="number" id="productName" class="form-control" type="text" value="{{$price_code->number}}" readonly>
                                    <input name="id" id="id" class="form-control" type="text" value="{{$price_code->id}}" hidden>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Code</label>
                                    <input name="code" class="form-control" type="text" value="{{$price_code->code}}">
                                </div>
                            </div>
                            <!-- end row -->
                            <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Update </button>
                    </div>
                </div>
            </div> <!-- end col -->

            </form>
        </div>

    </div>
</div>



<!-- Java Script validation for empty form -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#myForm').validate({
            rules: {
                number: {
                    required: true,
                },
                code: {
                    required: true,
                },
                
            },
            messages: {
                number: {
                    required: 'Please Enter the Number',
                },
                category_id: {
                    required: 'Please select the Code',
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
            }
    });
    });

</script>
@endsection