@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">ECS Engineering</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row pb-2">
            <div class="col-6">
                @php
                $total_capital = 0;
                $all_data = App\Models\Product::get();
                foreach($all_data as $data){
                $total_capital += $data->product_buying_price * $data->quantity;
                }
                @endphp
                <!-- <h3>Total Capital : <span style="color:crimson">৳ {{ number_format($total_capital,2) }} Tk</span></h3> -->
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
                <h4 style="margin-top: 40px;">{{ $filterName }} Sales Overview</h4>

            </div>
            <div class="col-6">
                <a href="{{ url('/dashboard') }}" class="btn btn-dark btn-rounded waves-effect waves-dark mx-2" style="float:right; margin-top:30px"><i class="fa fa-undo"> Refresh </i></a>
                <!-- <a href="{{ route('daily.invoice.report') }}" class="btn btn-dark btn-rounded waves-effect waves-light" style="float:right;">
                <i class="fas fa-plus-circle"> Invoice Report </i>  -->
                <div class="form-content" style="float:right;">
                    <label for="dateFilter">Filter by Date:</label>
                    <select id="dateFilter" class="form-select shadow-none " onchange="updateDashboard()">
                        <option value="">Select</option>
                        <option value="today" {{ ($filter === 'today'?'selected':'') }}>Today</option>
                        <option value="yesterday" {{ ($filter === 'yesterday'?'selected':'') }}>Yesterday</option>
                        <option value="last7Days" {{ ($filter === 'last7Days'?'selected':'') }}>Last 7 Days</option>
                        <option value="last30Days" {{ ($filter === 'last30Days'?'selected':'') }}>Last 30 Days</option>
                        <option value="thisMonth" {{ ($filter === 'thisMonth'?'selected':'') }}>This Month</option>
                        <option value="lastMonth" {{ ($filter === 'lastMonth'?'selected':'') }}>Last Month</option>
                        <option value="thisMonthLastYear" {{ ($filter === 'thisMonthLastYear'?'selected':'') }}>This Month Last Year</option>
                        <option value="thisYear" {{ ($filter === 'thisYear'?'selected':'') }}>This Year</option>
                        <option value="lastYear" {{ ($filter === 'lastYear'?'selected':'') }}>Last Year</option>
                        <option value="currentFinancialYear" {{ ($filter === 'currentFinancialYear'?'selected':'') }}>Current Financial Year</option>
                        <option value="lastFinancialYear" {{ ($filter === 'lastFinancialYear'?'selected':'') }}>Last Financial Year</option>
                        <option value="customRange" {{ ($filter === 'customRange'?'selected':'') }}>Custom Range</option>
                    </select>
                    <div id="customRangeInputs" style="display:none;" class="pt-2">
                        <label for="customStartDate">Start Date:</label>
                        <input type="date" id="customStartDate" class="shadow-none form-select-sm">
                        <label for="customEndDate">End Date:</label>
                        <input type="date" id="customEndDate" class="shadow-none form-select-sm">
                        <button class="btn btn-dark" onclick="updateDashboardWithCustomRange()">Apply</button>
                    </div>
                </div>
                </a>
            </div>

        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Sales</p>
                                <h4 class="mb-2">৳ {{ (!empty($total_amount)?number_format($total_amount,2):'0') }} Tk</h4>
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
                                <h4 class="mb-2">৳ {{(!empty($total_paid)?number_format($total_paid,2):'0')}} Tk</h4>
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
                                <h4 class="mb-2">৳ {{(!empty($total_due)?number_format($total_due,2):'0')}} Tk</h4>
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
                                <p class="text-truncate font-size-14 mb-2">Profit</p>
                                <h4 class="mb-2">৳ {{(!empty($total_profit)?number_format($total_profit,2):'0')}} Tk</h4>
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
            <!-- end col -->
        </div>
        <!-- end row -->
        <!-- end page title -->
        <div class="row">
            <!-- Top-Selling Products -->
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice-title">
                                    <h5>
                                        <img src="{{asset('backend/assets/images/logo-sm.png')}}" alt="logo" height="18" /> Top-Selling Products
                                    </h5>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td class="text-start"><strong>#</strong></td>
                                                <td class="text-start"><strong>Name</strong></td>
                                                <td class="text-end"><strong>Quantity</strong></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($top_selling_products as $key => $value)
                                            <tr>
                                                <td class="text-start">{{$key+1}}</td>
                                                <td class="text-start">{{$value['product']->name}}</td>
                                                <td class="text-end">{{ number_format($value['total_sold'], 0) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> <!-- end row -->
                    </div>
                </div>
            </div> 
            <!-- Low Stock Alerts -->
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice-title">
                                    <h5>
                                        <img src="{{asset('backend/assets/images/logo-sm.png')}}" alt="logo" height="20" /> Low Stock Alerts
                                    </h5>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td class="text-start"><strong>#</strong></td>
                                                <td class="text-start"><strong>Name</strong></td>
                                                <td class="text-end"><strong>Quantity</strong></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($low_stock_products as $key => $value)
                                            <tr>
                                                <td class="text-start">{{$key+1}}</td>
                                                <td class="text-start">{{$value->name}}</td>
                                                <td class="text-end">{{ number_format($value->quantity, 0) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> <!-- end row -->
                    </div>
                </div>
            </div> 
            <!-- Out-of-Stock product -->
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice-title">
                                    <h5>
                                        <img src="{{asset('backend/assets/images/logo-sm.png')}}" alt="logo" height="20" /> Out-of-Stock Alerts
                                    </h5>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <td class="text-start"><strong>#</strong></td>
                                                <td class="text-start"><strong>Name</strong></td>
                                                <td class="text-end"><strong>Quantity</strong></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($out_of_stock_products as $key => $value)
                                            <tr>
                                                <td class="text-start">{{$key+1}}</td>
                                                <td class="text-start">{{$value->name}}</td>
                                                <td class="text-end">{{ number_format($value->quantity, 0) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> <!-- end row -->
                    </div>
                </div>
            </div> 
            
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="invoice-title">
                                    <h5>
                                        <img src="{{asset('backend/assets/images/logo-sm.png')}}" alt="logo" height="20" /> {{ $filterName }} Sales Overview
                                    </h5>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <td class="text-start"><strong>#</strong></td>
                                                <td class="text-center"><strong>Customer/Company Name</strong></td>
                                                <td class="text-center"><strong>Invoice No</strong></td>
                                                <td class="text-center"><strong>Date</strong></td>
                                                <td class="text-end"><strong>Total</strong></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                            @php
                                            $total_sum = '0';
                                            @endphp
                                            @foreach($allData as $item)
                                            <tr>
                                                <td class="text-start">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ (!empty($item['payment']['customer']['name'])?$item['payment']['customer']['name']:'Null') }}</td>
                                                <td class="text-center"># {{ $item['invoice_no'] }}</td>
                                                <td class="text-center">{{date('d/m/y', strtotime($item->date))}}</td>
                                                <td class="text-end">৳ {{ number_format($item['payment']['total_amount']) }} Tk</td>
                                            </tr>
                                            @php
                                            $total_sum += $item['payment']['total_amount'];
                                            @endphp
                                            @endforeach

                                            <tfoot>

                                                <td colspan="4" class="text-end">
                                                    <h5>Total</h5>
                                                </td>
                                                <td colspan="1" class="text-end">
                                                    <h5 class="m-0">৳ {{ number_format($total_sum,2) }} Tk</h5>
                                                </td>
                                            </tfoot>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $.fn.dataTable.ext.errMode = 'none';

        $('#datatable-10').DataTable({
            pageLength: 10
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
                return;
        }

        customRangeInputs.style.display = 'none';
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
        // alert(formattedStartDate + formattedEndDate);
        var url = '{{ url()->current() }}?'
        url += '&startDate=' + formattedStartDate;
        url += '&endDate=' + formattedEndDate;
        url += '&filter=' + filter;
        window.location.href = url;

    }
</script>
@endsection