<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invoice #{{$purchase->id}} - Printed At: {{ now()->format('d-m-Y  h:i A') }} - Printed By: {{ Auth::user()->name }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Bootstrap Css -->
    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100" style="margin-left: 42px;">

    <script>
        function printDiv() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
        window.onload = function() {
            printDiv();

            // Execute performAction after 500ms (0.5 seconds)
            setTimeout(performAction, 500);
        };

        function performAction() {
            @if($redirect === 'purchase-all')
            window.location.href = '{{ route("purchase.all") }}';
            @endif
            @if($redirect === 'purchase-edit')
            window.location.href = '{{ route("purchase.all") }}';
            @endif
        }
    </script>
    @php
    $payment = App\Models\SupplierPurchesePayment::where('purchase_id',$purchase->id)->first();
    @endphp
    <div class="invoice-container flex-fill" id="printableArea">
        <style>
            .invoice-container {
                font-family: 'Segoe UI', sans-serif;
                position: relative;
            }

            .invoice-watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                opacity: 0.05;
                z-index: -1;
                width: 400px;
                height: auto;
                filter: grayscale(100%);
                pointer-events: none;
                /* So it doesn’t interfere with clicks */
            }

            .invoice-header {
                color: #000;
                text-align: center;
                border-bottom: 2px solid #000;
                padding-bottom: 1rem;
                margin-bottom: .5rem;
            }

            .company-logo {
                max-width: 400px;
                margin: 0;
            }

            .invoice-details {
                color: #000;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                margin: .5rem 0;
            }

            .invoice-table {
                width: 100%;
                border-collapse: collapse;
                margin: 1rem 0;
            }

            .invoice-table th {
                font-size: 13px;
                background-color: #f8f9fa;
                padding: 10px;
                border: 1px solid #000;
            }

            .invoice-table td {
                color: #000;
                font-size: 13px;
                padding: 8px;
                border: 1px solid #000;
            }

            .total-section {
                padding: 1rem 0rem;
                margin-top: 2rem;
            }

            .total-row {
                color: #000;
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }

            @media print {
                @page {
                    size: A4;
                    margin-top: 12.7mm;
                    margin-right: 12.7mm;

                    /* optional: top/right/bottom/left margin */
                }
                .modal-header,
                .btn-close {
                    display: none !important;
                }

                .invoice-watermark {
                    opacity: 0.05;
                }

                .invoice-container {
                    padding: 0;
                    font-size: 12pt;
                }

                .page-break {
                    page-break-after: always;
                }

                tr {
                    page-break-inside: avoid;
                }

                footer {
                    color: #000;
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    background: white;
                    padding: 0.5rem 1rem;
                    margin-top: 100px !important;
                    /* border-top: 1px solid #000; */
                }
            }
        </style>
        <img src="{{ asset('backend/assets/images/logo-dark.png') }}" class="invoice-watermark" alt="Watermark">
        <div class="invoice-header">
            <img src="{{ asset('backend/assets/images/logo-dark.png') }}" class="company-logo" alt="Company Logo" style="text-align: center; display: block; margin: 0 auto;">
            <p class="mb-0 " style="font-size: 1rem;">
                Mob: 01730 430806, 01943 336105 | Email: masbah@ecsbd.net,
                sales@ecsbd.net
            </p>
            <p class="mb-0" style="font-size: 0.9rem;">
                Eastern Housing (2nd Pharse), Alubdi Bazar, Pallabi, Mirpur-12, Dhaka
            </p>
        </div>
        <h3 style="text-align: center; margin: 0rem 0rem;"><b>Purchase</b></h3>
        <div class="invoice-details" style="font-size: 1rem;  border-top: 2px solid #000;">
            <div class="mt-2" style="text-align: start; line-height: 1;">
                <p class="mb-1"><strong>{{ $payment->supplier_id != -1 ? $payment['supplier']['name'] : "পথচারি কাস্টমার"}}</strong></p>
                <p class="mb-1">Email: {{$payment->supplier_id != -1 ? $payment['supplier']['email'] :'N/A' }}</p>
                <p class="mb-1">Phone: {{$payment->supplier_id != -1 ? $payment['supplier']['mobile_no'] :'N/A' }}</p>
                <p class="mb-1">Office Address: {{$payment->supplier_id != -1 ? $payment['supplier']['office_address'] :'N/A' }}</p>
                <p class="mb-1">Contact Person Name & Phone: {{$payment->supplier_id != -1 ? $payment['supplier']['contact_person_name'] :'N/A' }} {{$payment['supplier']['contact_person_phone'] ? '('.$payment['supplier']['contact_person_phone'].')':'' }}</p>
            </div>
            <div class="text-end mt-2">
                <p class="mb-1"><strong>Date:</strong> {{date('d/m/Y', strtotime($purchase->date))}}</p>
                <p class="mb-1"><strong>Purchase #:</strong> {{ $purchase->purchase_no}}</p>
                @if ($purchase->wo_no)
                <p class="mb-1"><strong>WO NO #:</strong> {{ $purchase->wo_no}}</p>
                @endif
            </div>
        </div>
        @php
        if($purchase['supplier_purchese_details']->count()>6){
        $firstPageChunk = collect($purchase['supplier_purchese_details'])->take(12);
        $remainingChunks = collect($purchase['supplier_purchese_details'])->skip(12)->chunk(13);
        }
        @endphp
        @if ($purchase['supplier_purchese_details']->count()>6)
            @if ($firstPageChunk->isNotEmpty())
            <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 0px; font-size: 1rem;">
                <thead style="background-color: #dc3545; color: black; font-weight: bold;">
                    <tr style="background-color: #dc3545; color: black; font-weight: bold;">
                        <th style="padding: 8px 12px; text-align: left;">#</th>
                        <th style="padding: 8px 12px; text-align: left;">Product</th>
                        <th style="padding: 8px 12px; text-align: center;">Brand</th>
                        <th style="padding: 8px 12px; text-align: center;">Qty</th>
                        <th style="padding: 8px 12px; text-align: center;">Unit</th>
                        <th style="padding: 8px 12px; text-align: right;">Unit Price (Tk)</th>
                        <!-- <th style="padding: 8px 12px; text-align: right;">VAT/ TAX</th> -->
                        <th style="padding: 8px 12px; text-align: right;">Discount (Tk)</th>
                        <th style="padding: 8px 12px; text-align: right;">Total (Tk)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total_selling_price = 0;
                    @endphp
                    @foreach($firstPageChunk as $key=>$details)
                    <tr style="border-bottom: 1px solid #000;">
                        <td style="padding: 8px 12px;">{{ $key+1 }}</td>
                        <td style="padding: 8px 12px; text-align: left;">{{ $details['product']['name'] }}
                            
                        </td>
                        <td style="padding: 8px 12px; text-align: center;">{{ !empty($details['product']['brand']['name']) ? $details['product']['brand']['name'] : '' }}</td>
                        <td style="padding: 8px 12px; text-align: center;">{{ $details->buying_qty }}</td>
                        <td style="padding: 8px 12px; text-align: center;">{{ $details['product']['unit']->name}}</td>
                        <td style="padding: 8px 12px; text-align: right;">{{ number_format($details->product_buying_price, 2) }}</td>
                        <!-- <td style="padding: 8px 12px; text-align: right;">
                            @php
                            $taxIds = json_decode($details->tax_type, true);
                            $taxes = \App\Models\Tax::whereIn('id', $taxIds)->get();
                            $taxList = $taxes->map(fn($tax) => "{$tax->name}")->implode(', '); // Format & join with comma
                            @endphp
                            @if (count($taxIds) > 0)
                            ({{ $taxList }})
                            <br />
                            @endif
                            {{ number_format($details->tax_amount,2) }}
                        </td> -->
                        <td style="padding: 8px 12px; text-align: right;">{{$details->discount_type == 'percentage' ? $details->discount_rate . '%' : number_format($details->discount_amount, 2)}} {{$details->discount_type == 'percentage' ? '('.number_format($details->discount_amount,2).')' : ''}}</td>
                        <td style="padding: 8px 12px; text-align: right;">{{ number_format($details->selling_price, 2) }}</td>
                    </tr>
                    @php
                    $total_selling_price += $details->selling_price;
                    @endphp
                    @endforeach
                    {{-- Only show page break if there are more items --}}
                    
                </tbody>
            </table>
            @endif
            {{-- Remaining chunks (7 per page) with top margin --}}
            @foreach ($remainingChunks as $chunk)
            <div class="page-break"></div>
            <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 0px; font-size:1rem;">
                <thead style="background-color: #dc3545; color: black; font-weight: bold;">
                    <tr style="background-color: #dc3545; color: black; font-weight: bold;">
                        <th style="padding: 8px 12px; text-align: left;">#</th>
                        <th style="padding: 8px 12px; text-align: left;">Product</th>
                        <th style="padding: 8px 12px; text-align: center;">Brand</th>
                        <th style="padding: 8px 12px; text-align: center;">Qty</th>
                        <th style="padding: 8px 12px; text-align: center;">Unit</th>
                        <th style="padding: 8px 12px; text-align: right;">Unit Price (Tk)</th>
                        <!-- <th style="padding: 8px 12px; text-align: right;">VAT/ TAX</th> -->
                        <th style="padding: 8px 12px; text-align: right;">Discount (Tk)</th>
                        <th style="padding: 8px 12px; text-align: right;">Total (Tk)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunk as $key=>$details)
                    <tr style="border-bottom: 1px solid #000;">
                        <td style="padding: 8px 12px;">{{ $key+1 }}</td>
                        <td style="padding: 8px 12px; text-align: left;">{{ $details['product']['name'] }}
                           
                        </td>
                        <td style="padding: 8px 12px; text-align: center;"> {{ !empty($details['product']['brand']['name']) ? $details['product']['brand']['name'] : '' }}</td>
                        <td style="padding: 8px 12px; text-align: center;">{{ $details->buying_qty }}</td>
                        <td style="padding: 8px 12px; text-align: center;">{{ $details['product']['unit']->name}}</td>
                        <td style="padding: 8px 12px; text-align: right;">{{ number_format($details->product_buying_price, 2) }}</td>
                        <!-- <td style="padding: 8px 12px; text-align: right;">
                            @php
                            $taxIds = json_decode($details->tax_type, true);
                            $taxes = \App\Models\Tax::whereIn('id', $taxIds)->get();
                            $taxList = $taxes->map(fn($tax) => "{$tax->name}")->implode(', '); // Format & join with comma
                            @endphp
                            @if (count($taxIds) > 0)
                            ({{ $taxList }})
                            <br />
                            @endif
                            {{ number_format($details->tax_amount,2) }}
                        </td> -->
                        <td style="padding: 8px 12px; text-align: right;">{{$details->discount_type == 'percentage' ? $details->discount_rate . '%' : number_format($details->discount_amount, 2)}} {{$details->discount_type == 'percentage' ? '('.number_format($details->discount_amount,2).')' : ''}}</td>
                        <td style="padding: 8px 12px; text-align: right;">{{ number_format($details->selling_price, 2) }}</td>
                    </tr>
                    @php
                    $total_buying_price += $details->buying_price;
                    @endphp
                    @endforeach

                    @if ($purchase->purchase_discount_amount != 0)
                    <tr style="font-weight: 400; ">
                        <td colspan="7" class="thick-line text-end">
                            <strong>Purchase Discount</strong>
                        </td>
                        <td class="text-end">{{ number_format($purchase->purchase_discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if ($total_buying_price != 0)
                    <tr style="font-weight: 400; ">
                        <td colspan="7" class="thick-line text-end">
                            <strong>Sub-total</strong>
                        </td>
                        <td class="text-end">{{ number_format($total_buying_price-$purchase->purchase_discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if ($payment->discount_amount != 0)
                    <tr style="font-weight: 400; padding:0;">
                        <td colspan="7" class="thick-line text-end">
                            <strong>Total Discount</strong>
                        </td>
                        <td class="text-end">{{ number_format($payment->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @php
                    $purchase_tax_ids = json_decode($purchase->purchase_tax_type, true);
                    $taxes = \App\Models\Tax::whereIn('id', $purchase_tax_ids)->get();
                    $taxList = $taxes->map(fn($tax) => "{$tax->name}")->implode(', '); // Format & join with comma
                    @endphp
                    @if (count($purchase_tax_ids)>0)
                    <tr style="font-weight: 400; padding:0;">
                        <td colspan="7" class="thick-line text-end">
                            <strong>Invoice VAT({{$taxList}})</strong>
                        </td>
                        <td class="text-end">{{ number_format($purchase->purchase_tax_type, 2) }}</td>
                    </tr>
                    @endif
                    @if ($payment->total_tax_amount > 0)
                    <tr style="font-weight: 400; padding:0;">
                        <td colspan="7" class="thick-line text-end">
                            <strong>Total VAT</strong>
                        </td>
                        <td class="text-end">{{ number_format($payment->total_tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    @php
                    $purchase_additional_charge_ids = json_decode($purchase->additional_charge_type, true);
                    $additional_fees = \App\Models\AdditionalFee::whereIn('id', $purchase_additional_charge_ids)->get();
                    $additional_fee_list = $additional_fees->map(fn($additional_fee) => "{$additional_fee->name}")->implode(', '); // Format & join with comma
                    @endphp
                    @if (count($purchase_additional_charge_ids) > 0)
                    <tr style="font-weight: 400; padding:0;">
                        <td colspan="7" class="thick-line text-end">
                            <strong>Total Additional Charge({{ $additional_fee_list }})</strong>
                        </td>
                        <td class="text-end">{{ number_format($payment->total_additional_charge_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="font-weight: 400; padding:0;">
                        <td colspan="7" class="thick-line text-end">
                            <strong>Total Payable</strong>
                        </td>
                        <td class="text-end">{{ number_format($payment->total_amount, 2) }}</td>
                    </tr>

                    @if ($payment->paid_amount > 0)
                    <tr style="font-weight: 400; padding:0;">
                        <td colspan="7" class="thick-line text-end">
                            <strong>Paid Amount</strong>
                        </td>
                        <td class="text-end">{{ number_format($payment->paid_amount, 2) }}</td>
                    </tr>
                    @endif

                    <!-- @if ($payment->due_amount > 0)
                    <tr style="font-weight: 600; padding:0;">
                        <td colspan="7" class="thick-line text-end" >
                            <strong>Due Amount</strong>
                        </td>
                        <td class="text-end" >{{ number_format($payment->due_amount, 2) }}</td>
                    </tr>
                    @endif -->

                    <!-- @if ($pre_due != 0)
                    <tr style="font-weight: 600; padding:0;">
                        <td colspan="7" class="thick-line text-end" >
                            <strong>Previous Due</strong>
                        </td>
                        <td class="text-end" >{{ number_format($pre_due, 2) }}</td>
                    </tr>
                    @endif -->

                    <!-- @if (($pre_due + $payment->due_amount) != 0)
                    <tr style="font-weight: 600; color: #dc3545; padding:0;">
                        <td colspan="7" class="thick-line text-end" >
                            <strong>Total Due</strong>
                        </td>
                        <td class="text-end" >{{ number_format($pre_due + $payment->due_amount, 2) }}</td>
                    </tr>
                    @endif -->
                </tbody>
            </table>
            @endforeach
        @else

        <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 0px; font-size: 1rem;">
            <thead style="background-color: #dc3545; color: black; font-weight: bold;">
                <tr style="background-color: #dc3545; color: black; font-weight: bold;">
                    <th style="padding: 8px 12px; text-align: left;">#</th>
                    <th style="padding: 8px 12px; text-align: left;">Product</th>
                    <th style="padding: 8px 12px; text-align: center;">Brand</th>
                    <th style="padding: 8px 12px; text-align: center;">Qty</th>
                    <th style="padding: 8px 12px; text-align: center;">Unit</th>
                    <th style="padding: 8px 12px; text-align: right;">Unit Price (Tk)</th>
                    <!-- <th style="padding: 8px 12px; text-align: right;">VAT/ TAX</th> -->
                    <th style="padding: 8px 12px; text-align: right;">Discount (Tk)</th>
                    <th style="padding: 8px 12px; text-align: right;">Total (Tk)</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total_buying_price = 0;
                @endphp
                @foreach($purchase['supplier_purchese_details'] as $details)
                <tr style="border-bottom: 1px solid #000;">
                    <td style="padding: 8px 12px;">{{ $loop->iteration }}</td>
                    <td style="padding: 8px 12px; text-align: left;">{{ $details['product']['name'] }}

                    </td>
                    <td style="padding: 8px 12px; text-align: center;">{{ !empty($details['product']['brand']['name']) ? $details['product']['brand']['name']  : '' }}</td>
                    <td style="padding: 8px 12px; text-align: center;">{{ $details->buying_qty }}</td>
                    <td style="padding: 8px 12px; text-align: center;">{{ $details['product']['unit']->name}}</td>
                    <td style="padding: 8px 12px; text-align: right;">{{ number_format($details->product_buying_price, 2) }}</td>
                    <!-- <td style="padding: 8px 12px; text-align: right;">
                        @php
                        $taxIds = json_decode($details->tax_type, true);
                        $taxes = \App\Models\Tax::whereIn('id', $taxIds)->get();
                        $taxList = $taxes->map(fn($tax) => "{$tax->name}")->implode(', '); // Format & join with comma
                        @endphp
                        @if (count($taxIds) > 0)
                        ({{ $taxList }})
                        <br />
                        @endif
                        {{ number_format($details->tax_amount,2) }}
                    </td> -->
                    <td style="padding: 8px 12px; text-align: right;">{{$details->discount_type == 'percentage' ? $details->discount_rate . '%' : number_format($details->discount_amount, 2)}} {{$details->discount_type == 'percentage' ? '('.number_format($details->discount_amount,2).')' : ''}}</td>
                    <td style="padding: 8px 12px; text-align: right;">{{ number_format($details->buying_price, 2) }}</td>
                </tr>
                @php
                $total_buying_price += $details->buying_price;
                @endphp
                @endforeach

                @if ($purchase->purchase_discount_amount != 0)
                <tr style="font-weight: 400; ">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Purchase Discount</strong>
                    </td>
                    <td class="text-end">{{ number_format($purchase->purchase_discount_amount, 2) }}</td>
                </tr>
                @endif
                @if ($total_buying_price != 0)
                <tr style="font-weight: 400; ">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Sub-total</strong>
                    </td>
                    <td class="text-end">{{ number_format($total_buying_price-$purchase->purchase_discount_amount, 2) }}</td>
                </tr>
                @endif
                @if ($payment->discount_amount != 0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Total Discount</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->discount_amount, 2) }}</td>
                </tr>
                @endif
                @php
                $purchase_tax_ids = json_decode($purchase->purchase_tax_type, true);
                $taxes = \App\Models\Tax::whereIn('id', $purchase_tax_ids)->get();
                $taxList = $taxes->map(fn($tax) => "{$tax->name}")->implode(', '); // Format & join with comma
                @endphp
                @if (count($purchase_tax_ids)>0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Purchase VAT({{$taxList}})</strong>
                    </td>
                    <td class="text-end">{{ number_format($purchase->purchase_tax_amount, 2) }}</td>
                </tr>
                @endif
                @if ($payment->total_tax_amount > 0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Total VAT</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->total_tax_amount, 2) }}</td>
                </tr>
                @endif
                @php
                $purchase_additional_charge_ids = json_decode($purchase->additional_charge_type, true);
                $additional_fees = \App\Models\AdditionalFee::whereIn('id', $purchase_additional_charge_ids)->get();
                $additional_fee_list = $additional_fees->map(fn($additional_fee) => "{$additional_fee->name}")->implode(', '); // Format & join with comma
                @endphp
                @if (count($purchase_additional_charge_ids) > 0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Total Additional Charge({{ $additional_fee_list }})</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->total_additional_charge_amount, 2) }}</td>
                </tr>
                @endif
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Total Payable</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->total_amount, 2) }}</td>
                </tr>

                @if ($payment->paid_amount > 0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Paid Amount</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->paid_amount, 2) }}</td>
                </tr>
                @endif

                <!-- @if ($payment->due_amount > 0)
                <tr style="font-weight: 600; padding:0;">
                    <td colspan="7" class="thick-line text-end" >
                        <strong>Due Amount</strong>
                    </td>
                    <td class="text-end" >{{ number_format($payment->due_amount, 2) }}</td>
                </tr>
                @endif -->

                <!-- @if ($pre_due != 0)
                <tr style="font-weight: 600; padding:0;">
                    <td colspan="7" class="thick-line text-end" >
                        <strong>Previous Due</strong>
                    </td>
                    <td class="text-end" >{{ number_format($pre_due, 2) }}</td>
                </tr>
                @endif -->

                <!-- @if (($pre_due + $payment->due_amount) != 0)
                <tr style="font-weight: 600; color: #dc3545; padding:0;">
                    <td colspan="7" class="thick-line text-end" >
                        <strong>Total Due</strong>
                    </td>
                    <td class="text-end" >{{ number_format($pre_due + $payment->due_amount, 2) }}</td>
                </tr>
                @endif -->
            </tbody>
        </table>

        @endif

        @if ($purchase['supplier_purchese_details']->count()>6 && !$remainingChunks->isNotEmpty())
        <div class="page-break"></div>
        <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 0px; font-size: 1rem;">
            <thead style="background-color: #dc3545; color: black; font-weight: bold;">
                <tr style="background-color: #dc3545; color: black; font-weight: bold;">
                    <th style="padding: 8px 12px; text-align: left;">#</th>
                    <th style="padding: 8px 12px; text-align: left;">Product</th>
                    <th style="padding: 8px 12px; text-align: center;">Brand</th>
                    <th style="padding: 8px 12px; text-align: center;">Qty</th>
                    <th style="padding: 8px 12px; text-align: center;">Unit</th>
                    <th style="padding: 8px 12px; text-align: right;">Unit Price (Tk)</th>
                    <!-- <th style="padding: 8px 12px; text-align: right;">VAT/ TAX</th> -->
                    <th style="padding: 8px 12px; text-align: right;">Discount (Tk)</th>
                    <th style="padding: 8px 12px; text-align: right;">Total (Tk)</th>
                </tr>
            </thead>
            <tbody>

                @if ($purchase->purchase_discount_amount != 0)
                <tr style="font-weight: 400; ">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Purchase Discount</strong>
                    </td>
                    <td class="text-end">{{ number_format($purchase->purchase_discount_amount, 2) }}</td>
                </tr>
                @endif
                @if ($total_buying_price != 0)
                <tr style="font-weight: 400; ">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Sub-total</strong>
                    </td>
                    <td class="text-end">{{ number_format($total_buying_price-$purchase->purchase_discount_amount, 2) }}</td>
                </tr>
                @endif
                @if ($payment->discount_amount != 0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Total Discount</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->discount_amount, 2) }}</td>
                </tr>
                @endif
                @php
                $purchase_tax_ids = json_decode($purchase->purchase_tax_type, true);
                $taxes = \App\Models\Tax::whereIn('id', $purchase_tax_ids)->get();
                $taxList = $taxes->map(fn($tax) => "{$tax->name}")->implode(', '); // Format & join with comma
                @endphp
                @if (count($purchase_tax_ids)>0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Purchase VAT({{$taxList}})</strong>
                    </td>
                    <td class="text-end">{{ number_format($purchase->purchase_tax_ids, 2) }}</td>
                </tr>
                @endif
                @if ($payment->total_tax_amount > 0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Total VAT</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->total_tax_amount, 2) }}</td>
                </tr>
                @endif
                @php
                $purchase_additional_charge_ids = json_decode($purchase->additional_charge_type, true);
                $additional_fees = \App\Models\AdditionalFee::whereIn('id', $purchase_additional_charge_ids)->get();
                $additional_fee_list = $additional_fees->map(fn($additional_fee) => "{$additional_fee->name}")->implode(', '); // Format & join with comma
                @endphp
                @if (count($purchase_additional_charge_ids) > 0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Total Additional Charge({{ $additional_fee_list }})</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->total_additional_charge_amount, 2) }}</td>
                </tr>
                @endif
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Total Payable</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->total_amount, 2) }}</td>
                </tr>

                @if ($payment->paid_amount > 0)
                <tr style="font-weight: 400; padding:0;">
                    <td colspan="7" class="thick-line text-end">
                        <strong>Paid Amount</strong>
                    </td>
                    <td class="text-end">{{ number_format($payment->paid_amount, 2) }}</td>
                </tr>
                @endif

                <!-- @if ($payment->due_amount > 0)
                <tr style="font-weight: 600; padding:0;">
                    <td colspan="7" class="thick-line text-end" >
                        <strong>Due Amount</strong>
                    </td>
                    <td class="text-end" >{{ number_format($payment->due_amount, 2) }}</td>
                </tr>
                @endif -->

                <!-- @if ($pre_due != 0)
                <tr style="font-weight: 600; padding:0;">
                    <td colspan="7" class="thick-line text-end" >
                        <strong>Previous Due</strong>
                    </td>
                    <td class="text-end" >{{ number_format($pre_due, 2) }}</td>
                </tr>
                @endif -->

                <!-- @if (($pre_due + $payment->due_amount) != 0)
                <tr style="font-weight: 600; color: #dc3545; padding:0;">
                    <td colspan="7" class="thick-line text-end" >
                        <strong>Total Due</strong>
                    </td>
                    <td class="text-end" >{{ number_format($pre_due + $payment->due_amount, 2) }}</td>
                </tr>
                @endif -->
            </tbody>
        </table>
        @endif

        <div class="total-section my-0 py-0">
            <div class="total-row" style="display: flex; justify-content: space-between; font-size: 0.7rem;">
                <span style=" width: 80%; display: inline-block;">
                    <b>IN WORDS: </b><strong>{{ strtoupper(convertNumberToWords($payment->total_amount)) }} TK ONLY</strong>
                </span>

            </div>
        </div>
        <div class="total-section">
            <div class="total-row" style="display: flex; justify-content: space-between; font-size: 0.7rem;">
                <span style="border-top: 1px solid #000; padding-top: 5px; width: 20%; display: inline-block">
                    ECS Engineer signature
                </span>
                <span style="border-top: 1px solid #000; padding-top: 5px; width: 20%; display: inline-block; text-align: right">
                    Receipt By:<br />
                    <span>{{$payment['supplier']->name}}</span>
                </span>
            </div>
        </div>

    </div>
    <footer class="text-center mt-1 text-muted d-flex justify-content-center px-3"
        style="font-size: .6rem; text-align: right; margin-left: 42px; border-top: 1px solid #000;">
        <div class="page-number"></div>
        <small>ECS Engineering | Purchase No: {{$purchase->id}} | Generated By: {{ Auth::user()->name }} | At: {{ now()->format('d-m-Y  h:i A') }}</small>
    </footer>
</body>

</html>