@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">EDIT SUPPLIER</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('supplier.all')}}"> BACK </a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('supplier.update')}}" id="myForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $supplier->id }}">

                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Name</label>
                                    <input name="name" class="form-control" value="{{ $supplier->name }}" type="text">
                                </div>
                            </div>
                            <!-- Phone Numbers -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Primary Phone</label>
                                    <input name="mobile_no" class="form-control" type="text" placeholder="Enter primary phone number" value="{{ $supplier->mobile_no }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Secondary Phone</label>
                                    <input name="alt_mobile_no" class="form-control" type="text" placeholder="Enter secondary phone number" value="{{ $supplier->alt_mobile_no }}">
                                </div>
                            </div>
                            <!-- Emails -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Primary Email</label>
                                    <input name="email" class="form-control" type="email" placeholder="Enter primary email address" value="{{ $supplier->email }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Secondary Email</label>
                                    <input name="alt_email" class="form-control" type="email" placeholder="Enter secondary email address" value="{{ $supplier->alt_email }}">
                                </div>
                            </div>
                            <!-- Contact Person Info -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Contact Person Name</label>
                                    <input name="contact_person_name" class="form-control" type="text" placeholder="Enter contact person's name" value="{{ $supplier->contact_person_name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Contact Person Mobile</label>
                                    <input name="contact_person_phone" class="form-control" type="text" placeholder="Enter contact person's phone number" value="{{ $supplier->contact_person_phone }}">
                                </div>
                            </div>

                            <!-- Addresses -->
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Office Address</label>
                                    <input name="office_address" class="form-control" type="text" placeholder="Enter office address" value="{{ $supplier->office_address }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Factory Address</label>
                                    <input name="factory_address" class="form-control" type="text" placeholder="Enter factory address" value="{{ $supplier->factory_address }}">
                                </div>
                            </div>

                            <!-- Customer Image -->
                            <div class="row mb-3">
                                <label for="example-text-input" class="col-sm-2 col-form-label">Customer Image</label>
                                <div class="form-group col-sm-12">
                                    <input name="supplier_image" class="form-control" type="file" id="image">
                                </div>
                            </div>
                            <!-- end row -->

                            <div class="row mb-3">
                                <div class="col-sm-1    ">
                                    <label for="example-text-input" class="col-sm-2 col-form-label"></label>
                                    <img id="showImage" class="rounded avatar-lg" src="{{asset($supplier->supplier_image) }}" alt="Card image cap">
                                </div>
                            </div>
                            <!-- end row Customer Image -->
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Update
                            </button>
                            <a href="{{ route('supplier.all') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>

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
                name: {
                    required: true,
                },
                mobile_no: {
                    required: true,
                },
                email: {
                    required: true,
                },
                address: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: 'Please Enter the supplier  name',
                },
                mobile_no: {
                    required: 'Please Enter the supplier mobile number',
                },
                email: {
                    required: 'Please Enter the supplier valid email',
                },
                address: {
                    required: 'Please Enter the supplier address',
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

<!-- Customer Image show -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>


@endsection