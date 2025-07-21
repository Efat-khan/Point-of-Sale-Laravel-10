@extends('admin.admin_master')
@section('admin')
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
<div class="container-fluid">

<div class="row">
<div class="col-12">
    <div class="card">
        <div class="card-body">

            <h4 class="card-title">Daily Return Report  </h4><br><br>

            <form method="GET" action="{{route('daily.return.pdf')}}" target="_blank">
    <div class="row">

        <div class="col-md-4">
            <div class="md-3">
                <label for="example-text-input" class="form-label">Start Date</label>
                 <input class="form-control example-date-input" name="start_date" type="date" value="{{ date('Y-m-d') }}"  id="start_date">
            </div>
        </div>
        <div class="col-md-4">
            <div class="md-3">
                <label for="example-text-input" class="form-label">End Date</label>
                 <input class="form-control example-date-input" name="end_date" type="date" id="end_date" value="{{ date('Y-m-d') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="md-3">
                <label for="example-text-input" class="form-label" style="margin-top: 43px;"></label>
                <button type="submit" class="btn btn-info">Search</button>
                
            </div>
        </div>
    </div> <!-- // end row  --> 
    </form>

 

        </div> <!-- End card-body -->
<!--  ---------------------------------- -->

    </div>
</div> <!-- end col -->
</div>


</div>
</div>



@endsection 