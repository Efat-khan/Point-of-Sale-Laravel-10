@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit User</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('users.index')}}">ALL USERS</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('users.update',$user->id) }}" id="myForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- User Name -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Name</label>
                                    <input name="name" class="form-control" type="text" placeholder="Enter name" value="{{ $user->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">User Name</label>
                                    <input name="username" class="form-control" type="text" placeholder="Enter username" value="{{ $user->username }}">
                                </div>
                            </div>

                            <!-- Emails -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Email</label>
                                    <input name="email" class="form-control" type="email" placeholder="Enter email address" value="{{ $user->email }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Password(Default Password: user)</label>
                                    <input name="password" class="form-control" type="password" placeholder="Enter password">
                                </div>
                            </div>
                            <!-- User Role -->
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Roles</label>
                                    <select name="roles[]" multiple="multiple" id="roles" class="form-group form-select">
                                        <!-- <option value="TaxFree">VAT Free</option> -->
                                        @foreach ($roles as $key=>$value)
                                        <option value="{{$value->name}}" {{ $user->hasRole($value->name)?'selected':'' }}>{{$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- User Image Upload -->
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">User Image</label>
                                    <input name="profile_image" class="form-control" type="file" id="image">
                                </div>
                            </div>

                            <!-- Image Preview -->
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <img id="showImage" class="rounded avatar-lg" src="{{ asset($user->profile_image??'upload/no_image.jpg') }}" alt="User image">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Update </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--Select2 Start -->
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#roles').select2({
            placeholder: "Select Roles",
            allowClear: true,
            width: '100%'
        });
    });
</script>
<!-- Java Script validation for empty form -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#myForm').validate({
            rules: {
                name: {
                    required: true,
                },
                username: {
                    required: true,
                },
                email: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: 'Please Enter the name',
                },
                mobile_no: {
                    required: 'Please Enter the username',
                },
                email: {
                    required: 'Please Enter the email',
                },
               
                // customer_image: {
                //     required : 'Please select the customer image',
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
<!-- User Image show -->
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