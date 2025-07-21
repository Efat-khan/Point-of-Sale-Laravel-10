<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>POS Invoice Print</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <script>
        function printDiv() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

        }
        window.onload = printDiv;

        function performAction() {
            window.location.href = '{{ route("getDashboardData")}}';
        }
        // Execute performAction after 30 seconds (1000 milliseconds) 1second
        setTimeout(performAction, 1000);
    </script>

    <div class="row mx-auto" id="printableArea">
        <style>
            .dashed-hr {
                border-bottom: 2px dashed #dddddd;
                display: block;
                margin: 5px 0;
            }

            @page {
                size: auto;
                margin: 0 15px !important;
            }

            @media print {
                * {
                    color: #000000 !important;
                    font-weight: 500 !important;
                }

                h2,
                h3,
                h4,
                h5,
                h6 {
                    font-weight: 700 !important;
                }

                .table {
                    width: 100%;
                    margin-bottom: 1rem;
                    color: #000000;
                    border-collapse: collapse;
                }

                .table td,
                .table th {
                    padding: .75rem;
                    vertical-align: top;
                    border-top: 1px solid #000000
                }

                .table thead th {
                    vertical-align: bottom;
                    border-bottom: 1px solid #000000
                }

                .table tbody+tbody {
                    border-top: 1px solid #000000
                }

                .table-sm td,
                .table-sm th {
                    padding: .3rem
                }

                .table-bordered {
                    border: 1px solid #000000
                }

                .table-bordered td,
                .table-bordered th {
                    border: 1px solid #000000
                }

                .table-bordered thead td,
                .table-bordered thead th {
                    border-bottom-width: 1px
                }

                .text-left {
                    text-align: left !important;
                }

                .text-right {
                    text-align: right !important;
                }

                .pl--0 {
                    padding-left: 0 !important;
                }

                .pr--0 {
                    padding-right: 0 !important;
                }

                /* .com-name{
                                                        font-size: 25px !important;
                                                        margin-bottom: -20px !important;
                                                    } */
            }
        </style>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-10">

                        <div class="row">
                            <div class="col-12">
                                <div class="invoice-title">
                                    <h3>
                                        <img src="{{asset('backend/assets/images/logo-sm.png')}}" alt="logo" height="24" /> Ovee Electric Enterprise
                                    </h3>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <address>
                                            <strong> Proprietor: Foyez Ullah Miazi</strong> <br>
                                            Munshirhat, Fulgazi, Feni <br>
                                            Munshirhat, Fulgazi, Feni <br>
                                            <h3 class="text-center">{{ $filterName }} Report</h3>
                                            <h5 class="text-center">{{date('d/m/y', strtotime($startDate))}} To {{date('d/m/y', strtotime($endDate))}}</h5>
                                        </address>

                                    </div>
                                    <div class="col-6 text-end">
                                        <address>

                                        </address>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 mt-4">
                                        <address>

                                        </address>
                                    </div>
                                    <div class="col-6 mt-4 text-end">
                                        <address>

                                        </address>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div>

                                    <div class="">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <td class="text-center"><strong>Sl</strong></td>
                                                        <td class="text-center"><strong>Customer Name</strong></td>
                                                        <td class="text-center"><strong>Invoice No</strong></td>
                                                        <td class="text-center"><strong>Date</strong></td>
                                                        <td class="text-center"><strong>Description</strong></td>
                                                        <td class="text-center"><strong>Amount</strong></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- foreach ($order->lineItems as $line) or some such thing here -->
                                                    @php
                                                    $allData = App\Models\Invoice::whereBetween('created_at', [$startDate, $endDate])->where('status', '1')->get();
                                                    $total_sum = '0';
                                                    @endphp
                                                    @foreach($allData as $key => $item)
                                                    <tr>

                                                        <td class="text-center">{{$key+1}}</td>
                                                        <td class="text-center">{{ (!empty($item['payment']['customer']['name'])?$item['payment']['customer']['name']:'Null') }}</td>
                                                        <td class="text-center"># {{ $item->invoice_no }}</td>

                                                        <td class="text-center">{{date('d/m/y', strtotime($item->date))}}</td>
                                                        <td class="text-center">{{ $item->description }}</td>
                                                        <td class="text-center">{{ $item['payment']['total_amount'] }}</td>
                                                    </tr>
                                                    @php
                                                    $total_sum += $item['payment']['total_amount'];
                                                    @endphp
                                                    @endforeach

                                                    <tr>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <!-- <td class="no-line"></td> -->
                                                        <td class="no-line text-center">
                                                            <strong>Grand Total</strong>
                                                        </td>
                                                        <td class="no-line text-Center">
                                                            <h4 class="pl-5">৳ {{ $total_sum }}</h4>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <!-- <td class="no-line"></td> -->
                                                        <td class="no-line text-center">
                                                            <strong> Total Paid</strong>
                                                        </td>
                                                        <td class="no-line text-Center">
                                                            <h4 class="pl-5">৳ {{ $total_paid }}</h4>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <!-- <td class="no-line"></td> -->
                                                        <td class="no-line text-center">
                                                            <strong> Total Due</strong>
                                                        </td>
                                                        <td class="no-line text-Center">
                                                            <h4 class="pl-5">৳ {{ $total_due }}</h4>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <td class="no-line"></td>
                                                        <!-- <td class="no-line"></td> -->
                                                        <td class="no-line text-center">
                                                            <strong> Total Profit</strong>
                                                        </td>
                                                        <td class="no-line text-Center">
                                                            <h4 class="pl-5">৳ {{ $total_profit }}</h4>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-print-none">
                                            <div class="float-end">
                                                <a href="" class="btn btn-primary waves-effect waves-light ms-2"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end row -->

                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    </div>

</body>

</html>