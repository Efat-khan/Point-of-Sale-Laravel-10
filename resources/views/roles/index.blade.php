@extends('admin.admin_master')
@section('admin')
<style>
  div.dataTables_wrapper div.dataTables_filter input {
    width: 400px !important;
  }
</style>

<div class="page-content">
  <div class="container-fluid">
    {{--
       <div class="row align-items-center mb-3">
      <!-- Dropdown -->
      <div class="col-md-6 col-sm-12">
        <select id="roles_filter" class="form-select" onchange="updateDashboard()">
          <option value="">Select User</option>
          @foreach($filter_roles as $value)
          <option value="{{ $value->id }}" {{ ($value_id != null && $value_id == $value->id)?'selected':'' }}>{{ $value->name }}</option>
    @endforeach
    </select>
  </div>

  <!-- Buttons -->
  <div class="col-md-6 col-sm-12">
    <div class="d-flex justify-content-end gap-2 mt-2 mt-md-0">
      <a href="{{ route('roles.index') }}" class="btn btn-outline-dark">
        <i class="fas fa-undo"></i> Reset
      </a>
      <a href="{{ route('customer.all-report-pdf', $customer_id ?? null) }}" class="btn btn-dark waves-effect waves-light">
        <i class="mdi mdi-printer"></i> Print
      </a>
      @can('role-create')
      <a href="{{ route('roles.create') }}" class="btn btn-success">
        <i class="fas fa-plus-circle"></i> ADD
      </a>
      @endcan
    </div>
  </div>
</div>
--}}
<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">ALL ROLES</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
          @can('role-create')
          <li class="m-2"><a href="{{route('roles.create')}}" class="btn btn-success"><i class="fas fa-plus-circle"></i> ADD ROLE </a></li>
          @endcan
        </ol>
      </div>
    </div>
  </div>
</div>
<!-- end page title -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
          <thead>
            <tr>
              <th>Sl</th>
              <th>Name</th>
              <th>Email</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $key => $value)
            <tr>
              <td> {{ $key+1}} </td>
              <td> {{ $value->name }} </td>
              <td> {{ $value->email }}</td>
              <td class="d-flex justify-content-center gap-1">
                <a href="{{ route('roles.show', $value->id) }}" class="btn btn-primary sm" title="Show Data">
                  <i class="fas fa-eye"></i>
                </a>
                @can('role-edit')
                <a href="{{ route('roles.edit', $value->id) }}" class="btn btn-info sm" title="Edit Data">
                  <i class="fas fa-edit"></i>
                </a>
                @endcan
                @can('role-delete')
                <form method="POST" action="{{ route('roles.destroy', $value->id) }}" class="delete-form">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger sm show_confirm" title="Delete Data">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
                @endcan
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div> <!-- end col -->
</div> <!-- end row -->
</div> <!-- container-fluid -->
</div> <!-- End Page-content -->

@endsection
@section('admin_custom_js')
<!-- Delete sweet alert -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.show_confirm');

    deleteButtons.forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault(); // prevent form from submitting immediately

        Swal.fire({
          title: 'Are you sure?',
          text: "This action cannot be undone!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            btn.closest('form').submit(); // submit the form if confirmed
          }
        });
      });
    });
  });
</script>
<script>
  $(document).ready(function() {
    $('#roles_filter').select2({
      placeholder: "Select Company",
      allowClear: true,
      width: '100%'
    });
  });
</script>
<script>
  function updateDashboard() {
    // Convert dates to a suitable format for your backend
    const roles_filter = document.getElementById('roles_filter').value;
    // alert(invoice_type_filter);
    var url = '{{ url()->current() }}?'
    url += '&roles_filter=' + roles_filter;
    window.location.href = url;
  }
</script>
@endsection