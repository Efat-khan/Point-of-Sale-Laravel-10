<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title> {{ !empty($customer->name) ? $customer->name :"" }} Transactions Report - Printed At: {{ now()->format('d-m-Y  h:i A') }} - Printed By: {{ Auth::user()->name }}</title>

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
      window.location.href = '/customer/report?customer_id={{ $customer->id }}';
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
    <h3 style="text-align: center; margin: 0rem 0rem;"><b>Transactions</b></h3>
    <div class="invoice-details" style="font-size: 0.8rem; border-top: 1px solid #000;"">
      <div class=" mt-2" style="text-align: start; line-height: 1.2;">
      <p class="mb-1"><b>{{ !empty($customer->name) ? $customer->name :"N/A" }}</b></p>
      <p class="mb-1"><b>Phone: </b></p>
      {{ !empty($customer->mobile_no) ? $customer->mobile_no :"N/A" }}{{ !empty($customer->alt_mobile_no) ? ', '.$customer->alt_mobile_no :"" }}
      <br>
      <p class="mb-1"><b>Email: </b></p>
      {{ !empty($customer->email) ? $customer->email :"N/A" }}{{ !empty($customer->alt_email) ? ', '.$customer->alt_email :"" }}
      <br>
      <p class="mb-1"><b>Office Address: </b></p> {{ !empty($customer->office_address) ? $customer->office_address :"N/A" }}<br>
    </div>
    <div class="text-end mt-2">
      <p class="mb-1"><b>Factory Address: </b></p> {{ !empty($customer->factory_address) ? $customer->factory_address :"N/A" }}<br>
      <p class="mb-1"><b>Contact Person Name: </b></p> {{ !empty($customer->contact_person_name) ? $customer->contact_person_name :"N/A" }}<br>
      <p class="mb-1"><b>Contact Person Phone: </b></p> {{ !empty($customer->contact_person_phone) ? $customer->contact_person_phone :"N/A" }}<br>
    </div>
  </div>
  <table class="invoice-table" style="width: 100%; border-collapse: collapse; margin-top: 0px; font-size: 0.6rem;">
    <thead style="background-color: #dc3545; color: black; font-weight: bold;">
      <tr>
        <th>#</th>
        <th>Invoice No</th>
        <th>Payment</th>
        <th>Payment Details</th>
        <th>Date</th>
        <th>Paid Amount</th>
      </tr>
    </thead>
    <tbody>
      @php
      $total_paid = 0;
      @endphp
      @foreach($groupedDetails as $key => $item)
      @php
      $invoiceIdsArray = explode(',', $item['invoice_ids']);
      $total_paid += $item['total_amount'];
      @endphp
      <tr>
        <td>{{ $key + 1 }}</td>
        <td>
          @foreach ($invoiceIdsArray as $invoiceId)
          <a href="{{ route('customer.edit.invoice', $invoiceId) }}">#{{ $invoiceId }}</a>
          @endforeach
        </td>
        <td>{{$item['payment_type']}}
          <br />{{$item['payment_type']?'Paid By: '.$item['payee_name']:''}}
          <br />{{$item['received_by']?'Received By: '.$item['received_by']:''}}
          <br />{{$item['remarks']?'Remarks: '.':'.$item['remarks']:''}}
        </td>
        <td>
          @if($item['payment_type'] == 'Bank Payment')
          {{$item['bank_name']?'Bank Name: '.$item['bank_name']:''}}
          <br />{{$item['bank_branch_name']?'Branch: '.$item['bank_branch_name']:''}}
          <br />{{$item['bank_account_number']? 'Account Number: '.$item['bank_account_number']:''}}
          <br />{{$item['bank_cheque_number']?'Check Number'.': '.$item['bank_cheque_number']:''}}
          <br />{{$item['bank_micr_code']?'MICR Code'.': '.$item['bank_micr_code']:''}}
          <br />{{$item['bank_check_issue_date']?'Check Issue Date'.': '.date('d-m-Y', strtotime($item['bank_check_issue_date'])):''}}
          <br />{{$item['bank_check_cleared_at']?'Check Cleared Date'.': '.date('d-m-Y', strtotime($item['bank_check_cleared_at'])):''}}

          @elseif($item['payment_type'] == 'Online Payment')
          {{$item['bank_name']?'Bank Name: '.$item['bank_name']:''}}
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
          <p>Hand Cash</p>
          @endif
          <br />{{$item['payment_type']? 'Status: '.$item['status']:''}}
        </td>
        <td>{{ date('d-m-Y', strtotime($item['created_at'])) }}</td>
        <td>৳ {{ number_format($item['total_amount'], 2) }} Tk</td>
      </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" class="text-end">
          <h5>Total</h5>
        </td>
        <td>
          <h5>৳ {{ number_format($total_paid, 2) }} Tk</h5>
        </td>
      </tr>
    </tfoot>
    </tbody>
  </table>
  </div>
  <footer class="text-center mt-1 text-muted d-flex justify-content-center px-3"
    style="font-size: .6rem; text-align: right; margin-left: 42px;">
    <small>ECS Engineering | Generated By: {{ Auth::user()->name }} | At: {{ now()->format('d-m-Y  h:i A') }} | Software by Munsoft IT (+8801840885553)</small>
  </footer>
</body>

</html>