@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        @if ($errors->any())
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @elseif(session()->has('message'))
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-{{session()->get('alert-type')=='success'?'success':'danger'}} alert-dismissible fade show" role="alert">
                        {{ session()->get('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Test SMS</h4>
                        <form action="{{route('settings.sms_test')}}" method="POST">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="text" onkeypress="return event.charCode>=48 && event.charCode<=57" minlength="11" maxlength="11" name="number" class="form-control" placeholder="01900000000" aria-label="01900000000" aria-describedby="basic-addon2">
                            </div>
                            <button type="submit" class="btn btn-success" id="basic-addon2"><i class="fas fa-plus-circle"></i> Sent </button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">SMS Configuration </h4>
                        <form action="{{route('settings.sms_config')}}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="api-url" class="form-label">API URL</label>
                                <input type="url" class="form-control" id="api-url" value="{{old('api_url')?old('api_url'):$sms_config?->url}}" name="api_url"
                                    placeholder="https://login.esms.com.bd/api/v3/sms/send/">
                            </div>
                            <div class="mb-3">
                                <label for="api-token" class="form-label">API Token</label>
                                <input type="text" class="form-control" id="api-token" value="{{old('api_token')?old('api_token'):$sms_config?->token}}" name="api_token"
                                    placeholder="182|NI8qfoiuOKJlGNppoEa3kiul1qaBI0j39yxV4Uc6">
                            </div>
                            <div class="mb-3">
                                <label for="sender-id" class="form-label">Sender ID</label>
                                <input type="text" class="form-control" id="sender-id" placeholder="8809600000045"
                                    value="{{old('sender_id')?old('sender_id'):$sms_config?->sender_id}}" name="sender_id">
                            </div>
                            @php
                                $status = old('status')?old('status'):$sms_config?->status;
                            @endphp
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="status" value="1" {{$status == 1? 'checked':''}} role="switch" id="_status" onchange="$('#status-label').html($(this).is(':checked')?'Active':'In-Active')">
                                <label class="form-check-label" for="_status" id="status-label">{{$status == 1? 'Active':'In-Active'}}</label>
                              </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus-circle"></i> Submit </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
