<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Challan #{{$invoice->id}} - Printed At: {{ now()->format('d-m-Y  h:i A') }} - Printed By: {{ Auth::user()->name }}</title>

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
            @if($redirect === 'invoice-edit')
            window.location.href = '{{ route("invoice.edit", $invoice->id) }}';
            @endif
            @if($redirect === 'invoice-add')
            window.location.href = '{{ route("invoice.add") }}';
            @endif
            @if($redirect === 'invoice-all')
            window.location.href = '{{ route("invoice.all") }}';
            @endif
        }
    </script>

    @php
    $payment = App\Models\Payment::where('invoice_id',$invoice->id)->first();
    @endphp
    <div class="invoice-container flex-grow-1" id="printableArea">
        <style>
            .invoice-container {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
                margin: 1.5rem 0;
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

                tr {
                    page-break-inside: avoid;
                }
                footer {
                    color: #000;
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    padding: 0.5rem 1rem;
                    border-top: 1px solid #000;
                    background: white;
                }
            }
        </style>
        <img src="{{ asset('backend/assets/images/logo-dark.png') }}" class="invoice-watermark" alt="Watermark">
        <div class="invoice-header">
            <img src="{{ asset('backend/assets/images/logo-dark.png') }}" class="company-logo" alt="Company Logo" style="text-align: center; display: block; margin: 0 auto;">
            <p>
                Mob: 01730 430806, 01943 336105 | Email: masbah@ecsbd.net, sales@ecsbd.net <br>
                Eastern Housing (2nd Pharse), Alubdi Bazar, Pallabi, Mirpur-12, Dhaka
            </p>
        </div>
        <h2 class="text-center"><b>Challan</b></h2>
        <div class="invoice-details" style="font-size: 1rem; border-top: 2px solid #000;">
            <div class="mt-2" style="text-align: start; line-height: 1;">
                <p class="mb-1"><strong>Name: {{ $payment->customer_id != -1 ? $payment['customer']['name'] : "পথচারি কাস্টমার"}}</strong></p>
                <p class="mb-1">Email: {{$payment->customer_id != -1 ? $payment['customer']['email'] :'N/A' }}</p>
                <p class="mb-1">Phone: {{$payment->customer_id != -1 ? $payment['customer']['mobile_no'] :'N/A' }}</p>
                <p class="mb-1">Office Address: {{$payment->customer_id != -1 ? $payment['customer']['office_address'] :'N/A' }}</p>
                <p class="mb-1">Contact Person: {{$payment->customer_id != -1 ? $payment['customer']['contact_person_name']:'N/A' }} {{$payment['customer']['contact_person_phone'] ? '('.$payment['customer']['contact_person_phone'].')':'' }}</p>
            </div>
            <div class="text-end mt-2" style="text-align: end;">
                <p class="mb-1"><strong>Date:</strong> {{date('d/m/Y', strtotime($invoice->date))}}</p>
                <p class="mb-1"><strong>DN NO #:</strong> {{$invoice->dn_no}}</p>
                @if ($invoice->wo_no)
                <p class="mb-1"><strong>WO NO:</strong> {{$invoice->wo_no}}</p>
                @endif
            </div>
        </div>
        <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 0px;"> {{-- <-- Add margin here --}}
            <thead style=" color: black; font-weight: bold;">
                <tr>
                    <th style="padding: 8px 12px; text-align: left;">#</th>
                    <th style="padding: 8px 12px; text-align: left;">Description</th>
                    <th style="padding: 8px 12px; text-align: center;">Brand</th>
                    <th style="padding: 8px 12px; text-align: center;">Qty</th>
                    <th style="padding: 8px 12px; text-align: center;">Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice['invoice_details'] as $key => $details)
                <tr style="border-bottom: 1px solid #000;">
                    <td style="padding: 8px 12px;">{{ $key + 1 }}</td>
                    <td style="padding: 8px 12px; text-align: left;">
                        <span style="font-size:.8rem;"><b>{{ $details['product']['name'] }}</b></span>
                        
                        <br>
                        {!! $details['product']['description'] !!}
                    </td>
                    <td style="padding: 8px 12px; text-align: center;">{{ !empty($details['product']['brand']['name']) ? $details['product']['brand']['name'] : '' }}</td>
                    <td style="padding: 8px 12px; text-align: center;">{{ $details->selling_qty }}</td>
                    <td style="padding: 8px 12px; text-align: center;">{{ $details['product']['unit']->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section mt-10">
            <div class="total-row" style="display: flex; justify-content: space-between; font-size: 0.7rem;">
                <span style="border-top: 1px solid #000; padding-top: 5px; width: 20%; display: inline-block">
                    ECS Engineer signature
                </span>
                <span style="border-top: 1px solid #000; padding-top: 5px; width: 20%; display: inline-block; text-align: right">
                    Receipt By:<br />
                    <span>{{$payment['customer']->name}}</span>
                    
            </div>
        </div>
    </div>
    <footer class="text-center mt-1 text-muted d-flex justify-content-center px-3"
        style="font-size: .6rem; text-align: right; margin-left: 42px;">
        <small>ECS Engineering | Challan No: {{$invoice->id}} | Generated By: {{ Auth::user()->name }} | At: {{ now()->format('d-m-Y  h:i A') }}</small>
    </footer>
</body>

</html>