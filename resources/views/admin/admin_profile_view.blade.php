@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Account</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('change.password')}}">CHANGE PASSWORD</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card"><br><br>
                    <center>
                        <img class="rounded-circle avatar-xl" src="{{ (!empty($adminData->profile_image)) ? asset($adminData->profile_image) : url('upload/no_image.jpg')}}" alt="Card image cap">
                    </center>
                    <div class="card-body">
                        <h4 class="card-title">Name: {{ $adminData->name}}</h4> <br>
                        <h4 class="card-title">Email: {{ $adminData->email}}</h4> <br>
                        <h4 class="card-title">Username: {{ $adminData->username}}</h4> <br>
                        <a class="btn btn-info" href="{{route('edit.profile')}}"><i class="fas fa-edit"></i> Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection