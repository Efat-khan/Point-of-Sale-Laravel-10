@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
       <div class="row">
        <div class="col-12">
                                <div class="card">
                                    <div class="card-body">

                                        <h4 class="card-title">Edit SR</h4> <br><br>

                                        <form method="POST" action="{{ route('sr.update')}}" id="myForm" enctype="multipart/form-data">
                                       @csrf
                                       <input type="hidden" name="id" value="{{ $sr->id }}">
                            
                                        <div class="row mb-3">
                                            <label for="example-text-input" class="col-sm-2 col-form-label">SR Name</label>
                                            <div class="form-group col-sm-10">
                                                <input name="name" class="form-control" value="{{ $sr->name }}" type="text">
                                            </div>
                                        </div>
                                        <!-- end row -->

                                        <div class="row mb-3">
                                            <label for="example-text-input" class="col-sm-2 col-form-label">SR Mobile No.</label>
                                            <div class="form-group col-sm-10">
                                                <input name="mobile_no" class="form-control" value="{{ $sr->mobile_no }}" type="number">
                                            </div>
                                        </div>
                                        <!-- end row -->
                                        <div class="row mb-3">
                                            <label for="example-text-input" class="col-sm-2 col-form-label">SR Email</label>
                                            <div class="form-group col-sm-10">
                                                <input name="email" class="form-control" value="{{ $sr->email }}" type="email">
                                            </div>
                                        </div>
                                        <!-- end row -->
                                        <div class="row mb-3">
                                            <label for="example-text-input" class="col-sm-2 col-form-label">SR Address</label>
                                            <div class="form-group col-sm-10">
                                                <input name="address" class="form-control" value="{{ $sr->address }}" type="text">
                                            </div>
                                        </div>
                                        <!-- end row -->

                                         <!-- sr Image -->
                                         <div class="row mb-3">
                                            <label for="example-text-input" class="col-sm-2 col-form-label">SR Image</label>
                                            <div class="form-group col-sm-10">
                                                <input name="sr_image" class="form-control" type="file" id="image">
                                            </div>
                                        </div>
                                        <!-- end row -->
                                      
                                        <div class="row mb-3">
                                        <label for="example-text-input" class="col-sm-2 col-form-label"></label>
                                            <div class="col-sm-10">
                                            <img id="showImage" class="rounded avatar-lg" src="{{asset($sr->sr_image) }}" alt="Card image cap"> 
                                            </div>
                                        </div> 
                                        <!-- end row sr Image -->

                                        <input type="submit" class="btn btn-info btn-rounded waves-effect waves-light" value="Update SR" >
                                    </div>
                                </div>
                            </div> <!-- end col -->
                            
            </form>
        </div>
                
    </div>
</div>




<!-- Java Script validation for empty form -->
<script type="text/javascript">
    $(document).ready(function (){
        $('#myForm').validate({
            rules: {
                name: {
                    required : true,
                },
                mobile_no: {
                    required : true,
                },
                // email: {
                //     required : true,
                // },
                // address: {
                //     required : true,
                // },
            },
            messages :{
                name: {
                    required : 'Please Enter the sr  name',
                },
                mobile_no: {
                    required : 'Please Enter the sr mobile number',
                },
                // email: {
                //     required : 'Please Enter the sr valid email',
                // },
                // address: {
                //     required : 'Please Enter the sr address',
                // },
            },
            errorElement : 'span', 
            errorPlacement: function (error,element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight : function(element, errorClass, validClass){
                $(element).addClass('is-invalid');
            },
            unhighlight : function(element, errorClass, validClass){
                $(element).removeClass('is-invalid');
            },
        });
    });
    
</script>

<!-- sr Image show -->
<script type="text/javascript">
    $(document).ready(function(){
        $('#image').change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $('#showImage').attr('src',e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>


@endsection