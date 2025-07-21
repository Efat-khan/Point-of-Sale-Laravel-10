<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title> Customer All Report - Printed At: {{ now()->format('d-m-Y  h:i A') }} - Printed By: {{ Auth::user()->name }}</title>

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
      window.location.href = "{{ route('customer.all') }}";
    }
  </script>
  <div class="invoice-container flex-fill" id="printableArea">
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
        text-align: center;
        border-bottom: 1px solid #000;
        padding-bottom: 1rem;
        margin-bottom: .5rem;
      }

      .company-logo {
        max-width: 400px;
        margin: 0;
      }

      .invoice-details {
        display: grid;
        color: #000;
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
        background-color: #f8f9fa;
        padding: 12px;
        font-size: 13px;
        border-bottom: 2px solid #000;
      }

      .invoice-table td {
        padding: 12px;
        font-size: 12px;
        color: #000;
        border-bottom: 1px solid #000;
      }

      .invoice-table th,
      .invoice-table td {
        border: 1px solid #000;
      }

      .total-section {
        padding: 1rem 0rem;
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
          position: fixed;
          bottom: 0;
          left: 0;
          width: 100%;
          background: white;
          padding: 0.5rem 1rem;
          margin-top: 100px !important;
          border-top: 1px solid #000;
        }
      }
    </style>
    <img src="{{ asset('backend/assets/images/logo-dark.png') }}" class="invoice-watermark" alt="Watermark">
    <div class="invoice-header">
      <img src="{{ asset('backend/assets/images/logo-dark.png') }}" class="company-logo" alt="Company Logo" style="text-align: center; display: block; margin: 0 auto;">
      <p class="mb-0 text-muted" style="font-size: 1rem;">
        Mob: 01730 430806, 01943 336105 | Email: masbah@ecsbd.net,
        sales@ecsbd.net
      </p>
      <p class="mb-0 text-muted" style="font-size: 0.9rem;">
        Eastern Housing (2nd Pharse), Alubdi Bazar, Pallabi, Mirpur-12, Dhaka
      </p>
    </div>
    <h3 style="text-align: center; margin: 0rem 0rem;"><b>All Customers Information</b></h3>
    <div class="invoice-details" style="font-size: 0.8rem; border-top: 1px solid #000;"">
    </div>
    <table class=" invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 0px; font-size: 0.6rem;">
      <thead style="background-color: #dc3545; color: black; font-weight: bold;">
        <tr>
          <th>#</th>
          <!-- <th>Image</th> -->
          <th>Customer</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Address</th>
          <th>Contact Person</th>
        </tr>
      </thead>
      <tbody>
        @foreach($customers as $key => $item)
        <tr>
          <td> {{ $key+1}} </td>
          <td> {{ $item->name }} </td>
          <td> {{ $item->mobile_no }}<br> {{ $item->alt_mobile_no?$item->alt_mobile_no:'' }} </td>
          <td> {{ $item->email }}<br>{{ $item->alt_email?$item->alt_email:'' }}</td>
          <td><b>Office:</b> {{ $item->office_address }}</br><b>Factory:</b> {{ $item->factory_address ? $item->factory_address:''}} </td>
          <td><b>Name: </b>{{ $item->contact_person_name?$item->contact_person_name:'Not Available' }}<br>
          <b>Phone: </b>{{ $item->contact_person_phone?'Phone: '.$item->contact_person_phone:'Not Available' }} </td>
        </tr>
        @endforeach
      </tbody>
      </table>
    </div>
    <footer class="text-center mt-1 text-muted d-flex justify-content-center px-3"
      style="font-size: .6rem; text-align: right; margin-left: 42px;">
      <small>ECS Engineering | Generated By: {{ Auth::user()->name }} | At: {{ now()->format('d-m-Y  h:i A') }} | Software by Munsoft IT (+8801840885553)</small>
    </footer>
</body>

</html>