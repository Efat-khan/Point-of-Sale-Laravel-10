<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Preview - Printed At: {{ now()->format('d-m-Y  h:i A') }} - Printed By: {{ Auth::user()->name }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <!-- Bootstrap Css -->
  <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100" style="margin-left: 42px;">
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
    <h3 style="text-align: center; margin: 0rem 0rem;"><b>Challan
    </b></h3>
    @php
    if ($data['customer_id'] == 0) {
    $customer_name = $data['name'] ?? 'N/A';
    $customer_email = $data['email'] ?? 'N/A';
    $customer_mobile_no = $data['mobile_no'] ?? 'N/A';
    $customer_address = $data['address'] ?? 'N/A';
    $customer_contact_person = '';
    } else {
    $customer = \App\Models\Customer::find($data['customer_id']);
    $customer_name = $customer?->name ?? 'N/A';
    $customer_email = $customer?->email ?? 'N/A';
    $customer_mobile_no = $customer?->mobile_no ?? 'N/A';
    $customer_address = $customer?->office_address ?? 'N/A';
    $customer_contact_person = $customer->contact_person_name ? $customer->contact_person_name . ' (' . $customer->contact_person_phone . ')' : 'N/A';
    }
    @endphp
    <div class="invoice-details" style="font-size: 1rem;  border-top: 2px solid #000;">
      <div class="mt-2" style="text-align: start; line-height: 1;">
        <p class="mb-1"><strong>{{ $data['customer_id'] != -1 ? $customer_name : "পথচারি কাস্টমার"}}</strong></p>
        <p class="mb-1">Email: {{$data['customer_id'] != -1 ? $customer_email :'N/A' }}</p>
        <p class="mb-1">Phone: {{$data['customer_id'] != -1 ? $customer_mobile_no :'N/A' }}</p>
        <p class="mb-1">Office Address: {{$data['customer_id'] != -1 ? $customer_address :'N/A' }}</p>
        <p class="mb-1">Contact Person Name & Phone: {{$data['customer_id'] != -1 ? $customer_contact_person :'' }}</p>
      </div>
      <div class="text-end mt-2">
        <p class="mb-1"><strong>Date:</strong> {{date('d/m/Y', strtotime($data['date']))}}</p>
        <p class="mb-1"><strong>Invoice #:</strong> {{ $data['invoice_no']}}</p>
        @if ($data['wo_no'])
        <p class="mb-1"><strong>WO NO #:</strong> {{ $data['wo_no']}}</p>
        @endif
      </div>
    </div>
    <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 0px; font-size: 1rem;">
      <thead style="background-color: #dc3545; color: black; font-weight: bold;">
        <tr style="background-color: #dc3545; color: black; font-weight: bold;">
          <th style="padding: 8px 12px; text-align: left;">#</th>
          <th style="padding: 8px 12px; text-align: left;">Description</th>
          <th style="padding: 8px 12px; text-align: center;">Brand</th>
          <th style="padding: 8px 12px; text-align: center;">Qty</th>
          <th style="padding: 8px 12px; text-align: center;">Unit</th>
        </tr>
      </thead>
      <tbody>
        @foreach($finalProducts as $key=>$details)
        <tr style="border-bottom: 1px solid #000;">
          <td style="padding: 8px 12px;">{{ $key+1 }}</td>
          <td style="padding: 8px 12px; text-align: left;">
          <span style="color: black; font-weight: bold;">{{ $details->product_name }}</span>
          <br>{!! $details->product_description !!}

          </td>
          <td style="padding: 8px 12px; text-align: center;">{{ !empty($details->brand) ? $details->brand : '' }}</td>
          <td style="padding: 8px 12px; text-align: center;">{{ $details->qty }}</td>
          <td style="padding: 8px 12px; text-align: center;">{{ $details->unit}}</td>
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
          <span>{{$customer_name}}</span>
        </span>
      </div>
    </div>

  </div>
  <footer class="text-center mt-1 text-muted d-flex justify-content-center px-3"
    style="font-size: .6rem; text-align: right;  border-top: 1px solid #000;">
    <small>ECS Engineering | Challan No: {{ $data['invoice_no']}} | Generated By: {{ Auth::user()->name }} | At: {{ now()->format('d-m-Y  h:i A') }}</small>
  </footer>
</body>

</html>