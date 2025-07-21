@extends('admin.admin_master')
@section('admin')
<style>
    div.dataTables_wrapper div.dataTables_filter input {
        width: 400px !important;
    }
</style>
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
        <div class="row align-items-end g-3">
            <!-- Start Date -->
            <div class="col-md-2 col-6">
                <label for="customStartDate" class="form-label">Start Date</label>
                <input type="date" id="customStartDate" name="customStartDate" class="form-control"
                    value="{{ !empty($show_start_date) ? $show_start_date : \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}">
            </div>

            <!-- End Date -->
            <div class="col-md-2 col-6">
                <label for="customEndDate" class="form-label">End Date</label>
                <input type="date" id="customEndDate" name="customEndDate" class="form-control"
                    value="{{ !empty($show_end_date) ? $show_end_date : \Carbon\Carbon::now()->format('Y-m-d') }}">
            </div>

            <!-- Apply Button -->
            <div class="col-md-1 col-12 d-grid">
                <button type="button" class="btn btn-dark" onclick="updateDashboardWithCustomRange()">Apply</button>
            </div>

            <!-- Quick Date Filter -->
            <div class="col-md-2 col-12">
                <label for="dateFilter" class="form-label">Quick Filter</label>
                <select id="dateFilter" name="dateFilter" class="form-select" onchange="updateDashboard()">
                    <option value="">Select</option>
                    <option value="today" {{ $filter === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ $filter === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last7Days" {{ $filter === 'last7Days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="last30Days" {{ $filter === 'last30Days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="thisMonth" {{ $filter === 'thisMonth' ? 'selected' : '' }}>This Month</option>
                    <option value="lastMonth" {{ $filter === 'lastMonth' ? 'selected' : '' }}>Last Month</option>
                    <option value="thisMonthLastYear" {{ $filter === 'thisMonthLastYear' ? 'selected' : '' }}>This Month Last Year</option>
                    <option value="thisYear" {{ $filter === 'thisYear' ? 'selected' : '' }}>This Year</option>
                    <option value="lastYear" {{ $filter === 'lastYear' ? 'selected' : '' }}>Last Year</option>
                    <option value="currentFinancialYear" {{ $filter === 'currentFinancialYear' ? 'selected' : '' }}>Current FY</option>
                    <option value="lastFinancialYear" {{ $filter === 'lastFinancialYear' ? 'selected' : '' }}>Last FY</option>
                </select>
            </div>
            <!-- Customer Filter -->
            <div class="col-md-2 col-12">
                <label for="supplier_filter" class="form-label">Supplier</label>
                <select id="supplier_filter" name="supplier_filter" class="form-select" onchange="updateDashboard()">
                    <option value="">Select Supplier</option>
                    @foreach($suppliers_for_filter as $supplier)
                    <option value="{{ $supplier->id }}" {{ ($supplier_filter == $supplier->id?'selected':'') }}>{{ $supplier->name }} - {{ $supplier->mobile_no }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Action Buttons -->
            <div class="col-md-3 mt-3 d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ route('purchase.supplier.all.transaction') }}" class="btn btn-outline-dark">
                    <i class="fas fa-undo"></i> Reset
                </a>
                <a href="{{ route('purchase.supplier.all.transaction-report-pdf',
                [!empty($show_start_date) ? $show_start_date : \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'),
                !empty($show_end_date)?$show_end_date:Carbon\Carbon::now()->format('Y-m-d'),
                $filter??'null',
                $supplier_filter??'null'
                ]) }}" class="btn btn-dark">
                    <i class="mdi mdi-printer"></i> Print
                </a>
            </div>
        </div>

        <!-- start page title -->
        <div class="row" style="padding-top: 10px !important;">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">SUPPLIER TRANSACTIONS</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('supplier.add')}}"> ADD SUPPLIER </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('supplier.all')}}"> ALL SUPPLIER </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('purchase.supplier_wise_purchese_payment.all')}}"> SUPPLIER DUE & PAYMENT</a></li>
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
                                    <th width="20px;">#</th>
                                    <th>Supplier</th>
                                    <th>Purchase No</th>
                                    <th>Payment</th>
                                    <th>Payment Details</th>
                                    <th>Date</th>
                                    <th>Paid Amount</th>
                                    <th width="20px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $total_paid = 0;
                                @endphp
                                @foreach($groupedDetails as $key => $item)
                                @php
                                $purchaseIdsArray = explode(',', $item['purchase_ids']);
                                $total_paid += $item['total_amount'];
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="fw-bold">{{ $item['supplier_name'] }}</span>
                                            <form action="{{ route('purchase.wise.due.report') }}" method="GET" target="_blank" class="mb-0">
                                                <input type="hidden" name="supplier_id" value="{{ $item['supplier_id'] }}">
                                                <button type="submit" class="btn btn-sm btn-link p-0" title="Supplier Purchase Details">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="text-muted">{{ $item['supplier_phone'] }}</div>
                                    </td>
                                    <td>
                                        @foreach ($purchaseIdsArray as $purchaseId)
                                        #{{ $purchaseId }}
                                        @endforeach
                                    </td>
                                    <td>{{$item['payment_type']}}
                                        <br />{{$item['payment_type']?'Paid By: '.$item['payee_name']:''}}
                                        <br />{{$item['received_by']?'Received By: '.$item['received_by']:''}}
                                        <br />{{$item['remarks']?'Remarks: '.':'.$item['remarks']:''}}
                                    </td>
                                    <td>
                                        @if($item['payment_type'] == 'Bank Payment')
                                        {{$item['bank_name']?'Bank Name: '.$item['bank_name']:''}} {{$item['payment_type']? '('.$item['status'].')':''}}
                                        <br />{{$item['bank_branch_name']?'Branch: '.$item['bank_branch_name']:''}}
                                        <br />{{$item['bank_account_number']? 'Account Number: '.$item['bank_account_number']:''}}
                                        <br />{{$item['bank_cheque_number']?'Check Number'.': '.$item['bank_cheque_number']:''}}
                                        <br />{{$item['bank_micr_code']?'MICR Code'.': '.$item['bank_micr_code']:''}}
                                        <br />{{$item['bank_check_issue_date']?'Check Issue Date'.': '.date('d-m-Y', strtotime($item['bank_check_issue_date'])):''}}
                                        <br />{{$item['bank_check_cleared_at']?'Check Cleared Date'.': '.date('d-m-Y', strtotime($item['bank_check_cleared_at'])):''}}

                                        @elseif($item['payment_type'] == 'Online Payment')
                                        {{$item['bank_name']?'Bank Name: '.$item['bank_name']:''}} {{$item['payment_type']? '('.$item['status'].')':''}}
                                        <br />{{$item['bank_branch_name']?'Branch: '.$item['bank_branch_name']:''}}
                                        <br />{{$item['online_transfer_method']?'Transfer Method: '.$item['online_transfer_method']:''}}
                                        <br />{{$item['online_transaction_id']?'Transaction ID: '.$item['online_transaction_id']:''}}
                                        <br />{{$item['sender_account_number']?'Sender Account Number: '.$item['sender_account_number']:''}}
                                        <br />{{$item['receiver_account_number']?'Receiver Account Number: '.$item['receiver_account_number']:''}}
                                        @elseif($item['payment_type'] == 'Mobile Banking')
                                        {{$item['mobile_banking_type']?'Mobile Banking: '.$item['mobile_banking_type']:''}}
                                        <br />{{$item['mobile_banking_account_type']?'Account type: '.$item['mobile_banking_account_type']:''}}
                                        <br />{{$item['mobile_banking_sender_number']?'Sender Number: '.$item['mobile_banking_sender_number']:''}}
                                        <br />{{$item['mobile_banking_receiver_number']?'Receiver Number: '.$item['mobile_banking_receiver_number']:''}}
                                        <br />{{$item['mobile_banking_transaction_id']?'Transaction ID: '.$item['mobile_banking_transaction_id']:''}}
                                        @else
                                        <p>Hand Cash {{$item['payment_type']? '('.$item['status'].')':''}}</p>
                                        @endif

                                    </td>
                                    <td>{{ date('d-m-Y', strtotime($item['created_at'])) }}</td>
                                    <td>৳ {{ number_format($item['total_amount'], 2) }} Tk</td>
                                    <td>
                                        @can('supplier-transaction-edit')
                                        <a href="{{ route('purchase.supplier.transaction.edit', $item['transaction_id']) }}" class="btn btn-info btn-sm" title="Edit Transaction">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        {{--
                                        <a href="{{ route('customer.transaction.delete', $item['transaction_id']) }}" class="btn btn-danger btn-sm" title="Delete Transaction" id="delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                          --}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Total Paid Amount:</th>
                                    <th>৳ {{ number_format($total_paid, 2) }} Tk</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div> <!-- End Page-content -->

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
            default:
                startDate = last30DaysStart;
                endDate = today;
                break;
                return;
        }
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
        const supplier_filter = document.getElementById('supplier_filter').value;
        // alert(invoice_type_filter);
        var url = '{{ url()->current() }}?'
        url += '&startDate=' + formattedStartDate;
        url += '&endDate=' + formattedEndDate;
        url += '&filter=' + filter;
        url += '&supplier_filter=' + supplier_filter;
        window.location.href = url;
    }
</script>
@endsection