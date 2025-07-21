<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invoice #{{$invoice->id}}</title>

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
            window.location.href = '{{ route("invoice.add")}}';
        }
        // Execute performAction after 30 seconds (1000 milliseconds) 1second
        setTimeout(performAction, 1000);
    </script>
    @php

    $payment = App\Models\Payment::where('invoice_id',$invoice->id)->first();
    @endphp

    <div class="invoice-container" id="printableArea">
        <style>
            .invoice-container {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                position: relative;
            }

            .invoice-watermark {
                position: absolute;
                width: 400px;
                height: auto;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                opacity: 0.1;
                z-index: -1;
                filter: grayscale(100%);
            }

            .invoice-header {
                text-align: center;
                border-bottom: 2px solid #dee2e6;
                padding-bottom: 1.5rem;
                margin-bottom: 2rem;
            }

            .company-logo {
                max-width: 300px;
                margin: 0 auto 1rem;
            }

            .invoice-details {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
                margin: 2rem 0;
            }

            .invoice-table {
                width: 100%;
                border-collapse: collapse;
                margin: 1.5rem 0;
            }

            .invoice-table th {
                background-color: #f8f9fa;
                padding: 12px;
                border-bottom: 2px solid #dee2e6;
            }

            .invoice-table td {
                padding: 10px;
                border-bottom: 1px solid #dee2e6;
            }

            .total-section {
                background-color: #f8f9fa;
                padding: 1rem;
                border-radius: 8px;
                margin-top: 2rem;
            }

            .total-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }

            .total-row {
                display: flex;
                justify-content: space-between;
            }

            @media print {

                .modal-header,
                .btn-close {
                    display: none !important;
                }

                .invoice-watermark {
                    opacity: 0.15;
                }

                .invoice-container {
                    padding: 0;
                    font-size: 12pt;
                }
            }
        </style>

        <img src="{{ asset('backend/assets/images/logo-dark.png') }}" class="invoice-watermark" alt="Watermark">

        <div class="invoice-header">
            <img src="{{ asset('backend/assets/images/logo-dark.png') }}" class="company-logo" alt="Company Logo">
            <p class="mb-0 text-muted" style="font-size: 0.7rem;">
                Mob: 01730 430806, 01943 336105 | Email: masbah@ecsbd.net,
                sales@ecsbd.net
            </p>
            <p class="mb-0 text-small" style="font-size: 0.6rem;">
                Eastern Housing (2nd Pharse), Alubdi Bazar, Pallabi, Mirpur-12, Dhaka
            </p>
        </div>
        <div class="invoice-details" style="margin-top: 0px;">
            <div>
                <p class="mb-1"><strong>Invoice #:</strong> {{$invoice->invoice_no}}</p>
                <p class="mb-1"><strong>Date:</strong> {{date('d/m/Y', strtotime($invoice->date))}}</p>
            </div>
            <div class="text-end">
                <p class="mb-1"><strong>Billed To:</strong></p>
                <p class="mb-1">{{ $payment->customer_id != -1 ? $payment['customer']['name'] : "পথচারি কাস্টমার"}}</p>
                <p class="mb-1">{{$payment->customer_id != -1 ? $payment['customer']['mobile_no'] :'N/A' }}</p>
            </div>
        </div>
        <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead style="background-color: #dc3545; color: black; font-weight: bold;">
                <tr style="background-color: #dc3545; color: black; font-weight: bold;">
                    <th style="padding: 8px 12px; text-align: left;">No.</th>
                    <th style="padding: 8px 12px; text-align: left;">Product</th>
                    <th style="padding: 8px 12px; text-align: center;">Qty</th>
                    <th style="padding: 8px 12px; text-align: right;">Unit Price</th>
                    <th style="padding: 8px 12px; text-align: right;">Discount</th>
                    <th style="padding: 8px 12px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total_sum = '0';
                @endphp
                @foreach($invoice['invoice_details'] as $details)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 8px 12px;">{{ $loop->iteration }}</td>
                    <td style="padding: 8px 12px; text-align: left;">{{ $details['product']['name'] }}
                        {{ !empty($details['product']['brand']['name']) ? '(' . $details['product']['brand']['name'] . ')' : '' }}
                    </td>
                    <td style="padding: 8px 12px; text-align: center;">{{ $details->selling_qty }}</td>
                    <td style="padding: 8px 12px; text-align: right;">৳{{ number_format($details->unit_price, 2) }}</td>
                    <td style="padding: 8px 12px; text-align: right;">৳{{ number_format($details->total_sell_commission, 2) }}</td>
                    <td style="padding: 8px 12px; text-align: right;">৳{{ number_format($details->selling_price, 2) }}</td>
                </tr>
                @php
                $total_sum += $details->selling_price;
                @endphp
                @endforeach

                @if ($total_sum != 0)
                <tr style="font-weight: 600; ">
                    <td colspan="5" class="thick-line text-end">
                        <strong>Sub-total</strong>
                    </td>

                    <td class="text-end">৳{{ number_format($total_sum, 2) }}</td>
                </tr>
                @endif

                @if ($payment->discount_amount != 0)
                <tr style="font-weight: 600; padding:0;">
                <td colspan="5" class="thick-line text-end">
                        <strong>Discount</strong>
                    </td>
                    <td class="text-end">৳{{ number_format($payment->discount_amount, 2) }}</td>
                </tr>
                @endif

                <tr style="font-weight: 600; padding:0;">
                <td colspan="5" class="thick-line text-end">
                        <strong>Total Payable</strong></td>
                    <td class="text-end">৳{{ number_format($payment->total_amount, 2) }}</td>
                </tr>

                @if ($payment->paid_amount != 0)
                <tr style="font-weight: 600; padding:0;">
                <td colspan="5" class="thick-line text-end">
                        <strong>Amount Paid</strong>
                    </td>
                    <td class="text-end">৳{{ number_format($payment->paid_amount, 2) }}</td>
                </tr>
                @endif

                @if ($payment->due_amount != 0)
                <tr style="font-weight: 600; padding:0;">
                <td colspan="5" class="thick-line text-end">
                        <strong>Current Due</strong>
                    </td>
                    <td class="text-end">৳{{ number_format($payment->due_amount, 2) }}</td>
                </tr>
                @endif

                @if ($pre_due != 0)
                <tr style="font-weight: 600; padding:0;">
                <td colspan="5" class="thick-line text-end">
                        <strong>Previous Due</strong>
                    </td>
                    <td class="text-end">৳{{ number_format($pre_due, 2) }}</td>
                </tr>
                @endif

                @if (($pre_due + $payment->due_amount) != 0)
                <tr style="font-weight: 600; color: #dc3545; padding:0;">
                <td colspan="5" class="thick-line text-end">
                        <strong>Total Due</strong>
                    </td>
                    <td class="text-end">৳</td>
                </tr>
                @endif
            </tbody>
        </table>

        <style>
            .invoice-table th,
            .invoice-table td {
                border: 1px solid #ddd;
            }

            /* .invoice-table tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            } */
        </style>





        <div class="total-section mt-10">
            <div class="total-row" style="display: flex; justify-content: space-between;">
                <span style="border-top: 1px solid #000; padding-top: 5px; width: 20%; display: inline-block">
                        ECS Engineer signature
                </span>
                <span style="border-top: 1px solid #000; padding-top: 5px; width: 20%; display: inline-block; text-align: right">
                    Receipt By:<br/>
                    <span style="font-size: 0.8rem;">Name:</span>
                    <span style="font-size: 0.8rem;">Post:</span>
                    <span style="font-size: 0.8rem;">Phone:</span>
                    
                </span>
            </div>
        </div>

        <div class="text-center mt-1 text-muted d-flex justify-content-between" style="border-top: 1px solid #dee2e6;
            padding-top: 1rem;">
            <small class="text-left">Printed At : </small>
            <small class="text-right">Software by Munsoft BD (01815229363)</small>
        </div>

    </div>

</body>

</html>