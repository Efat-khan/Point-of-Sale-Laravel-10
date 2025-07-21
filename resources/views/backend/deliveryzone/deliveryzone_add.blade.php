@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
       <div class="row">

            <form method="POST" action="{{ route('deliveryzone.store')}}" id="myForm">
                @csrf
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">

                                        <h4 class="card-title">Add Delivery Zone</h4> <br><br>
                                        <div class="row mb-3">
                                            <label for="example-text-input" class="col-sm-2 col-form-label">Delivery Zone Name</label>
                                            <div class="form-group col-sm-10">
                                                <input name="delivery_zone" class="form-control" type="text">
                                            </div>
                                        </div>
                                        <!-- end row -->

                                        <input type="submit" class="btn btn-info btn-rounded waves-effect waves-light" value="Add Delivery Zone" >
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
 
            },
            messages :{
                name: {
                    required : 'Please Enter the Delivery Zone name',
                },
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


@endsection