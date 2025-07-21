@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Account</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('admin.profile')}}">ACCOUNT</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <form method="POST" action="{{ route('store.profile')}}" enctype="multipart/form-data">
                @csrf
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <label for="text-input" class="col-sm-12 col-form-label">Name</label>
                                    <input name="name" class="form-control" type="text" value="{{$editData->name}}" id="example-text-input">
                                </div>
                                <div class="col-sm-6">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Email</label>
                                    <input name="email" class="form-control" type="email" value="{{$editData->email}}" id="example-text-input">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">UserName</label>
                                    <input name="username" class="form-control" type="text" value="{{$editData->username}}" id="example-text-input">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Profile Image</label>
                                    <input name="profile_image" class="form-control" type="file" id="image">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <label for="example-text-input" class="col-sm-2 col-form-label"></label>
                                <div class="col-sm-12">

                                    <!-- <img class="img-thumnail" id="showImage" width="200" src="{{ asset('backend/assets/images/small/img-5.jpg')}}" alt="Card image cap"> -->

                                    <img id="showImage" class="rounded avatar-lg" src="{{ (!empty($adminData->profile_image)) ? asset($adminData->profile_image) : asset('upload/no_image.jpg') }}" alt="Card image cap">
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