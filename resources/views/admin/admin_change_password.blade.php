@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Change Password</h4>
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
            @if(count($errors))
            @foreach ($errors->all() as $error)
            <p class="alert alert-danger alert-dismissible fade show"> {{$error}}</p>
            @endforeach
            @endif
            <form method="POST" action="{{ route('update.password')}}">
                @csrf
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Old Password</label>
                                    <input name="oldpassword" class="form-control" type="password" id="oldpassword">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">New Password</label>
                                    <input name="newpassword" class="form-control" type="password" id="newpassword">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Confirm Password</label>
                                    <input name="confirm_password" class="form-control" type="password" id="confirm_password">
                                </div>
                            </div>
                            <!-- end row -->
                            <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Change Password </button>
                        </div>
                    </div>
                </div> <!-- end col -->
            </form>
        </div>
    </div>
</div>
@endsection