@extends('admin.admin_master')
@section('admin')
@php
$filterName = 'Last 30 Days';

if($filter === 'today')
$filterName = 'Today';
elseif($filter === 'yesterday')
$filterName = 'Yesterday';

elseif($filter === 'last7Days')
$filterName = 'Last 7 Days';

elseif($filter === 'last30Days')
$filterName = 'Last 30 Days';

elseif($filter === 'thisMonth')
$filterName = 'This Month';

elseif($filter === 'lastMonth')
$filterName = 'Last Month';

elseif($filter === 'thisMonthLastYear')
$filterName = 'This Month Last Year';

elseif($filter === 'thisYear')
$filterName = 'This Year';

elseif($filter === 'lastYear')
$filterName = 'Last Year';

elseif($filter === 'currentFinancialYear')
$filterName = 'Current Financial Year';

elseif($filter === 'lastFinancialYear')
$filterName = 'Last Financial Year';

elseif($filter === 'customRange')
$filterName = $show_start_date.' To '.$show_end_date .' Date';
@endphp
<div class="page-content">
    <div class="container-fluid">
        <!-- Invoice Filter -->
        <div class="row g-2 align-items-end flex-wrap mb-3">
            {{-- Start Date --}}
            <div class="col-md-2">
                <label for="customStartDate" class="form-label mb-0 small">Start Date</label>
                <input type="date" id="customStartDate" name="customStartDate" class="form-control"
                    value="{{ $show_start_date ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}" required>
            </div>

            {{-- End Date --}}
            <div class="col-md-2">
                <label for="customEndDate" class="form-label mb-0 small">End Date</label>
                <input type="date" id="customEndDate" name="customEndDate" class="form-control"
                    value="{{ $show_end_date ?? \Carbon\Carbon::now()->format('Y-m-d') }}" required>
            </div>

            {{-- Apply Button --}}
            <div class="col-md-1">
                <label class="form-label mb-0 invisible">Apply</label>
                <button class="btn btn-dark w-100" onclick="updateDashboardWithCustomRange()">Apply</button>
            </div>

            {{-- Date Filter --}}
            <div class="col-md-2">
                <label for="dateFilter" class="form-label mb-0 small">Date Filter</label>
                <select id="dateFilter" class="form-select" onchange="updateDashboard()">
                    <option value="">Select</option>
                    <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $filter == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last7Days" {{ $filter == 'last7Days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last30Days" {{ $filter == 'last30Days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="thisMonth" {{ $filter == 'thisMonth' ? 'selected' : '' }}>This Month</option>
                    <option value="lastMonth" {{ $filter == 'lastMonth' ? 'selected' : '' }}>Last Month</option>
                    <option value="thisMonthLastYear" {{ $filter == 'thisMonthLastYear' ? 'selected' : '' }}>This Month Last Year</option>
                    <option value="thisYear" {{ $filter == 'thisYear' ? 'selected' : '' }}>This Year</option>
                    <option value="lastYear" {{ $filter == 'lastYear' ? 'selected' : '' }}>Last Year</option>
                    <option value="currentFinancialYear" {{ $filter == 'currentFinancialYear' ? 'selected' : '' }}>Current FY</option>
                    <option value="lastFinancialYear" {{ $filter == 'lastFinancialYear' ? 'selected' : '' }}>Last FY</option>
                </select>
            </div>

            {{-- Supplier Filter --}}
            <div class="col-md-2">
                <label for="supplier_filter" class="form-label mb-0 small">Supplier</label>
                <select id="supplier_filter" class="form-select" onchange="updateDashboard()">
                    <option value="">Select</option>
                    @foreach($all_suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ $supplier_filter == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }} - {{ $supplier->mobile_no }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Supplier Type Filter 
            <div class="col-md-1">
                <label for="invoice_type_filter" class="form-label mb-0 small">Type</label>
                <select id="invoice_type_filter" class="form-select" onchange="updateDashboard()">
                    <option value="">All</option>
                    <option value="draft" {{ $invoice_type_filter == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="challan" {{ $invoice_type_filter == 'challan' ? 'selected' : '' }}>Challan</option>
                    <option value="invoice" {{ $invoice_type_filter == 'invoice' ? 'selected' : '' }}>Invoice</option>
                </select>
            </div>--}}

            {{-- Right-Aligned Buttons --}}
            <div class="col-md-2 ms-auto d-flex gap-2">
                <div>
                    <label class="form-label mb-0 invisible">Reset</label>
                    <a href="{{ route('purchase.all') }}" class="btn btn-outline-dark">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
                @can('purchase-create')
                <div>
                    <label class="form-label mb-0 invisible">Add</label>
                    <a href="{{ route('purchase.add') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Add
                    </a>
                </div>
                @endcan
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Amount Spend</p>
                                <h4 class="mb-2" id="sales">৳ {{ (!empty($total_amount)?number_format($total_amount):'0') }} Tk</h4>
                                <h4 class="mb-2"></h4>
                                <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2">
                                        <!-- <i class="ri-arrow-right-up-line me-1 align-middle"></i></span>Invoices</p> -->
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-primary rounded-3">
                                    <i class="ri-shopping-cart-2-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Paid Amount</p>
                                <h4 class="mb-2" id="paid">৳ {{(!empty($total_paid)?number_format($total_paid):'0')}} Tk</h4>
                                <p class="text-muted mb-0"><span class="text-info fw-bold font-size-12 me-2">
                                        <!-- <i class="ri-arrow-right-up-line me-1 align-middle"></i>1.09%</span>from previous period</p> -->
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-success rounded-3">
                                    <i class="mdi mdi-currency-bdt font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Due Amount</p>
                                <h4 class="mb-2" id="due">৳ {{(!empty($total_due)?number_format($total_due):'0')}} Tk</h4>
                                <p class="text-muted mb-0"><span class="text-info fw-bold font-size-12 me-2">
                                        <!-- <i class="ri-arrow-right-up-line me-1 align-middle"></i>1.09%</span>from previous period</p> -->
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-success rounded-3">
                                    <i class="mdi mdi-currency-bdt font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div><!-- end col -->
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Total Supplier</p>
                                <h4 class="mb-2" id="profit"> {{(!empty($all_suppliers)?count($all_suppliers):'0')}} </h4>
                                <p class="text-muted mb-0"><span class="text-info fw-bold font-size-12 me-2">
                                        <!-- <i class="ri-arrow-right-up-line me-1 align-middle"></i>1.09%</span>from previous period</p> -->
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-success rounded-3">
                                    <i class="fas fa-users font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end cardbody -->
                </div><!-- end card -->
            </div><!-- end col -->
            <!-- end col -->
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="card-title">Purchase of - {{ $filterName }} </h4>
                            </div>
                            <div class="col-6">
                                <div class="text-sm-end">
                                    <a href="{{ route('print.purchase-all-filer',
                                        [!empty($show_start_date) ? $show_start_date : \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'),
                                        !empty($show_end_date)?$show_end_date:Carbon\Carbon::now()->format('Y-m-d'),
                                        $filter??'null',
                                        $supplier_filter??'null'
                                        ]) }}" target="_blank" class="btn btn-dark waves-effect waves-light mb-2 me-2"><i class="mdi mdi-printer"></i> Print </a>
                                </div>
                            </div>
                        </div>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20px;">#</th>
                                    <th >Company Name</th>
                                    <th>Purchase No </th>
                                    <th>Date</th>
                                    <th >Due Amount</th>
                                    <th >Total Amount</th>
                                    {{--<th>Status</th>--}}
                                    <th width="30px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allData as $key => $item)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td > {{ ($item['supplier_purchese_payment']['supplier_id'] != -1)? $item['supplier_purchese_payment']['supplier']['name']: 'পথচারি কাস্টমার' }} </td>
                                    <td>#{{ $item->purchase_no }}</td>
                                    <td> {{ date('d-m-Y',strtotime($item->date)) }} </td>
                                    <td > ৳ {{ number_format($item['supplier_purchese_payment']['due_amount'],2) }} Tk</td>
                                    <td > ৳ {{ number_format($item['supplier_purchese_payment']['total_amount'],2) }} Tk</td>
                                    {{--<td>
                                        @if($item->invoice_type == 'draft')
                                        <span class="btn btn-warning">Draft</span>
                                        @elseif($item->invoice_type == 'invoice')
                                        <span class="btn btn-success">Invoice</span>
                                        @elseif($item->invoice_type == 'challan')
                                        <span class="btn btn-primary">Challan</span>
                                        @endif
                                    </td>--}}
                                    <td >
                                       
                                        {{-- <a href="{{ route('print.invoice',$item->id) }}" class="btn btn-success sm" title="Print Invoice"> <i class="fas fa-print"></i> </a>--}}
                                        @can('purchase-edit')
                                        <a href="{{ route('purchase.edit',$item->id) }}" class="btn btn-info sm" title="Edit Purchase"> <i class="fas fa-edit"></i> </a>
                                        @endcan
                                        @can('purchase-delete')
                                        <a href="{{ route('purchase.destroy',$item->id) }}" class="btn btn-danger sm" title="Delete Purchase" id="ApproveBtn"> <i class="fas fa-trash-alt"></i> </a>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Grand Total</th>
                                    <th colspan="1">৳ {{ (!empty($total_due)?number_format($total_due):'0') }} Tk</th>
                                    <th colspan="1">৳ {{ (!empty($total_amount)?number_format($total_amount):'0') }} Tk</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div>
@endsection
@section('admin_custom_js')
<script>
    $(document).ready(function() {
        $('#supplier_filter').select2({
            placeholder: "Select Company",
            allowClear: true,
            width: '100%'
        });
    });
</script>
<script>
    function updateDashboard() {
        const filter = document.getElementById('dateFilter').value;
        const customRangeInputs = document.getElementById('customRangeInputs');
        let startDate, endDate;

        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);

        const last7DaysStart = new Date(today);
        last7DaysStart.setDate(today.getDate() - 7);

        const last30DaysStart = new Date(today);
        last30DaysStart.setDate(today.getDate() - 30);

        const currentMonthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        const currentMonthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);

        const thisMonthLastYearStart = new Date(today.getFullYear() - 1, today.getMonth(), 1);
        const thisMonthLastYearEnd = new Date(today.getFullYear() - 1, today.getMonth() + 1, 0);

        const currentYearStart = new Date(today.getFullYear(), 0, 1);
        const currentYearEnd = new Date(today.getFullYear(), 11, 31);

        const lastYearStart = new Date(today.getFullYear() - 1, 0, 1);
        const lastYearEnd = new Date(today.getFullYear() - 1, 11, 31);

        const financialYearStart = new Date(today.getFullYear(), 3, 1); // Assuming financial year starts in April
        const financialYearEnd = new Date(today.getFullYear() + 1, 2, 31);

        const lastFinancialYearStart = new Date(today.getFullYear() - 1, 3, 1);
        const lastFinancialYearEnd = new Date(today.getFullYear(), 2, 31);
        switch (filter) {
            case 'today':
                startDate = today;
                endDate = today;
                break;
            case 'yesterday':
                startDate = yesterday;
                endDate = yesterday;
                break;
            case 'last7Days':
                startDate = last7DaysStart;
                endDate = today;
                break;
            case 'last30Days':
                startDate = last30DaysStart;
                endDate = today;
                break;
            case 'thisMonth':
                startDate = currentMonthStart;
                endDate = currentMonthEnd;
                break;
            case 'lastMonth':
                startDate = lastMonthStart;
                endDate = lastMonthEnd;
                break;
            case 'thisMonthLastYear':
                startDate = thisMonthLastYearStart;
                endDate = thisMonthLastYearEnd;
                break;
            case 'thisYear':
                startDate = currentYearStart;
                endDate = currentYearEnd;
                break;
            case 'lastYear':
                startDate = lastYearStart;
                endDate = lastYearEnd;
                break;
            case 'currentFinancialYear':
                startDate = financialYearStart;
                endDate = financialYearEnd;
                break;
            case 'lastFinancialYear':
                startDate = lastFinancialYearStart;
                endDate = lastFinancialYearEnd;
                break;
            case 'customRange':
                customRangeInputs.style.display = 'block';
            default:
                startDate = last30DaysStart;
                endDate = today;
                break;
                return;
        }
        // customRangeInputs.style.display = 'none';
        fetchDashboardData(startDate, endDate);
    }

    function updateDashboardWithCustomRange() {
        const customStartDate = document.getElementById('customStartDate').value;
        const customEndDate = document.getElementById('customEndDate').value;
        if (customStartDate && customEndDate) {
            const startDate = new Date(customStartDate);
            const endDate = new Date(customEndDate);
            fetchDashboardData(startDate, endDate);
        } else {
            alert('Please select both start and end dates for the custom range.');
        }
    }

    function fetchDashboardData(startDate, endDate) {
        
        // Convert dates to a suitable format for your backend
        const formattedStartDate = startDate.toISOString().split('T')[0];
        const formattedEndDate = endDate.toISOString().split('T')[0];
        const filter = document.getElementById('dateFilter').value;
        // alert(filter);
        const supplier_filter = document.getElementById('supplier_filter').value;
        var url = '{{ url()->current() }}?'
        url += '&startDate=' + formattedStartDate;
        url += '&endDate=' + formattedEndDate;
        url += '&filter=' + filter;
        url += '&supplier_filter=' + supplier_filter;
        window.location.href = url;

    }
</script>
<script>
    function sendSMS(id, sr_id = 0) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('invoice.sms-send') }}",
            type: "POST",
            data: {
                id: id,
                sr_id: sr_id,
            },
            success: function(data) {
                alert(data.message);
                console.log(data);
                // console.log(data);
                // window.location.reload();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(XMLHttpRequest?.responseJSON?.message);

            }
        })
    }
</script>


@endsection