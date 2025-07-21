@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Add Supplier</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('supplier.all')}}">ALL SUPPLIER</a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('purchase.supplier.all.transaction')}}"> SUPPLIER TRANSACTIONS</a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('purchase.supplier_wise_purchese_payment.all')}}"> SUPPLIER DUE & PAYMENT</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('supplier.store') }}" id="myForm" enctype="multipart/form-data">
                @csrf
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- Customer Name -->
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Name</label>
                                    <input name="name" class="form-control" type="text" placeholder="Enter supplier or company name">
                                </div>
                            </div>

                            <!-- Phone Numbers -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Primary Phone</label>
                                    <input name="mobile_no" class="form-control" type="text" placeholder="Enter primary phone number">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Secondary Phone</label>
                                    <input name="alt_mobile_no" class="form-control" type="text" placeholder="Enter secondary phone number">
                                </div>
                            </div>

                            <!-- Emails -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Primary Email</label>
                                    <input name="email" class="form-control" type="email" placeholder="Enter primary email address">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Secondary Email</label>
                                    <input name="alt_email" class="form-control" type="email" placeholder="Enter secondary email address">
                                </div>
                            </div>

                            <!-- Contact Person Info -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Contact Person Name</label>
                                    <input name="contact_person_name" class="form-control" type="text" placeholder="Enter contact person's name">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Contact Person Mobile</label>
                                    <input name="contact_person_phone" class="form-control" type="text" placeholder="Enter contact person's phone number">
                                </div>
                            </div>

                            <!-- Addresses -->
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Office Address</label>
                                    <input name="office_address" class="form-control" type="text" placeholder="Enter office address">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Factory Address</label>
                                    <input name="factory_address" class="form-control" type="text" placeholder="Enter factory address">
                                </div>
                            </div>

                            <!-- Supplier Image Upload -->
                            <div class="row mb-3">
                                <div class="form-group col-md-10">
                                    <label class="form-label">Supplier / Company Image</label>
                                    <input name="supplier_image" class="form-control" type="file" id="image">
                                </div>
                            </div>

                            <!-- Image Preview -->
                            <div class="row mb-3">
                                <div class="col-sm-10">
                                    <img id="showImage" class="rounded avatar-lg" src="{{ url('upload/no_image.jpg') }}" alt="Supplier image">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-sm-10">
                                    
                                    <button type="submit" class="btn btn-success"><i class="fas fa-plus-circle"></i> Add </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
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
                office_address: {
                    required: true,
                },
                // supplier_image: {
                //     required : true,
                // },
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
                office_address: {
                    required: 'Please Enter the supplier address',
                },
                // supplier_image: {
                //     required : 'Please select the supplier image',
                // },
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

<!-- Supplier Image show -->
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