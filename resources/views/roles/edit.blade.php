@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Role</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('roles.index')}}">ALL ROlE</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('roles.update',$role->id) }}" id="myForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Role Name -->
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Name</label>
                                    <input name="name" class="form-control" type="text" placeholder="Enter name" value="{{ old('name', $role->name) }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="form-group col-md-12">
                                    <label class="form-label">Permissions</label>
                                    <select name="permissions[]" multiple="multiple" id="permissions" class="form-group form-select">
                                        <!-- <option value="TaxFree">VAT Free</option> -->
                                        @foreach ($permissions as $key=>$value)
                                        <option value="{{$value->name}}" {{ $role->hasPermissionTo($value->name)?'selected':'' }}>{{$value->name }}</option>
                                        @endforeach
                                    </select>
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
        $('#permissions').select2({
            placeholder: "Select Permissions",
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
                
            },
            messages: {
                name: {
                    required: 'Please Enter the name',
                }
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