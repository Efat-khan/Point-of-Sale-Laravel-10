@extends('admin.admin_master')
@section('admin')
<div class="page-content">
  <div class="container-fluid">
    <!-- start page title -->
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between pb-2  ">
          <h4 class="mb-sm-0">Supplier Report</h4>
          @php
          $total_due = '0';
          @endphp
          @foreach($allData as $key => $item)
          @php
          $total_due += $item->due_amount;
          @endphp
          @endforeach
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
              <li class="m-2 breadcrumb-item"><a href="{{route('purchase.supplier_wise_purchese_payment.all')}}" name="supplier_id">
                  BACK
                </a></li>
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

            <div class="row">
              <div class="col-12">
                <div class="row">
                  <div class="col-4">
                    <div class="invoice-title">

                      <h3>
                        <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="logo" height="24" /> ECS Engineering
                      </h3>
                    </div>
                  </div>
                  <div class="col-8 text-end d-print-none">
                    @can('supplier-transaction-create')
                    <!-- Make Payment -->
                    <button type="button" class="btn btn-success" data-bs-target="#staticBackdrop" onclick="makePayment('{{$supplier->id}}','{{ $supplier->mobile_no}}','{{ $supplier->name }}', '{{ $total_due }}')"><i class="fa fa-credit-card"></i> Make Payment</button>
                    @endcan

                    <!-- Show Invoices -->
                    <button type="button" class="btn btn-primary" onclick="showTable('purchase')">
                      <i class="fa fa-file-invoice"></i> Purchases
                    </button>

                    <!-- Show Transactions -->
                    <button type="button" class="btn btn-info" onclick="showTable('transaction')">
                      <i class="fa fa-list"></i> Transactions
                    </button>

                    <!-- Print Invoices -->
                    <a href="{{ route('purchase.supplier.purchase-report-pdf', $supplier->id) }}" class="btn btn-secondary">
                      <i class="fa fa-print"></i> Purchases
                    </a>

                    <!-- Print Transactions -->
                    <a href="{{ route('purchase.supplier.transaction-report-pdf', $supplier->id) }}" class="btn btn-secondary">
                      <i class="fa fa-print"></i> Transactions
                    </a>
                  </div>
                </div>
                <hr>
                <div class="row" style="color: #000;">
                  <div class="col-12">
                    <h3 class="font-size-16"><strong>Supplier Information</strong></h3>
                  </div>
                  <div class="col-6">
                    <address>
                      <strong><b>{{ !empty($supplier->name) ? $supplier->name :"N/A" }}</b></strong> <br>
                      <strong><b>Phone: </b></strong>
                      {{ !empty($supplier->mobile_no) ? $supplier->mobile_no :"N/A" }} {{ !empty($supplier->alt_mobile_no) ? $supplier->alt_mobile_no :"" }}
                      <br>
                      <strong><b>Email: </b></strong>
                      {{ !empty($supplier->email) ? $supplier->email :"N/A" }} {{ !empty($supplier->alt_email) ? $supplier->alt_email :"" }}
                      <br>
                      <strong><b>Office Address: </b></strong> {{ !empty($supplier->office_address) ? $supplier->office_address :"N/A" }}<br>
                    </address>
                  </div>
                  <div class="col-6 text-end">
                    <address>
                      <strong><b>Factory Address: </b></strong> {{ !empty($supplier->factory_address) ? $supplier->factory_address :"N/A" }}<br>
                      <strong><b>Contact Person Name: </b></strong> {{ !empty($supplier->contact_person_name) ? $supplier->contact_person_name :"N/A" }}<br>
                      <strong><b>Contact Person Phone: </b></strong> {{ !empty($supplier->contact_person_phone) ? $supplier->contact_person_phone :"N/A" }}<br>
                    </address>
                  </div>
                </div>
                <hr>
                <!-- Invoice Table -->
                <div id="purchaseTableWrapper">
                  <div class="row">
                    <div class="col-12">
                      <div class="p-2">
                        <h4 class="text-center"><b>All Purchase</b></h4>
                      </div>
                      <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
                          <thead>
                            <tr>
                              <th width="20px;">#</th>
                              <th>Purchase No</th>
                              <th>Date</th>
                              <th>Paid Amount</th>
                              <th>Due Amount</th>
                              <th>Total Amount</th>
                              <th width="30px;">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @php
                            $total_sell = 0;
                            $total_paid = 0;
                            $total_due = 0;
                            @endphp
                            @foreach($allData as $key => $item)
                            <tr>
                              <td>{{ $key + 1 }}</td>
                              <td>#{{ $item->purchase_id }}</td>
                              <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                              <td>{{ number_format($item->paid_amount, 2) }}</td>
                              <td>{{ number_format($item->due_amount, 2) }}</td>
                              <td>{{ number_format($item->total_amount, 2) }}</td>
                              <td>
                                @can('purchase-edit')
                                <a href="{{ route('purchase.edit',$item->id) }}" class="btn btn-info sm" title="Edit Purchase"> <i class="fas fa-edit"></i> </a>
                                @endcan
                                @can('purchase-delete')
                                <a href="{{ route('purchase.destroy',$item->id) }}" class="btn btn-danger sm" title="Delete Purchase" id="ApproveBtn"> <i class="fas fa-trash"></i> </a>
                                @endcan
                              </td>
                            </tr>
                            @php
                            $total_due += $item->due_amount;
                            $total_paid += $item->paid_amount;
                            $total_sell += $item->total_amount;
                            @endphp
                            @endforeach
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="3" class="text-end">
                                <h5>Total</h5>
                              </td>
                              <td>
                                <h5 class="text-success">৳ {{ number_format($total_paid, 2) }} Tk</h5>
                              </td>
                              <td>
                                <h5 class="text-danger">৳ {{ number_format($total_due, 2) }} Tk</h5>
                              </td>
                              <td>
                                <h5 class="text-info">৳ {{ number_format($total_sell, 2) }} Tk</h5>
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Transaction Table -->
                <div id="transactionTableWrapper" style="display: none;">
                  <div class="row">
                    <div class="col-12">
                      <div class="p-2">
                        <h4 class="text-center"><b>All Transaction</b></h4>
                      </div>
                      <div class="card-body">
                        <table class="table table-bordered dt-responsive nowrap" style="width: 100%;">
                          <thead>
                            <tr>
                              <th width="20px;">#</th>
                              <th>Purchase No</th>
                              <th>Payment</th>
                              <th>Payment Details</th>
                              <th>Date</th>
                              <th>Paid Amount</th>
                              <th width="30px;">Action</th>
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
                              <td colspan="5" class="text-end">
                                <h5>Total</h5>
                              </td>
                              <td>
                                <h5>৳ {{ number_format($total_paid, 2) }} Tk</h5>
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div> <!-- end row -->
          </div>
        </div>
      </div> <!-- end col -->
    </div> <!-- end row -->

  </div> <!-- container-fluid -->
</div>
<!--Make Payment Modal -->
<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Make Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <form action="{{route('purchase.supplier_wise_purchese_payment.make_payment')}}" method="POST" autocomplete="off">
          @csrf
          <input type="text" name="supplier_id" id="supplierId" hidden>
          <div class="mb-3">
            <h4 id="supplierName"></h4>
            <p>Mobile Number: <span id="supplierMobileNumber"></span></p>
            <h5 class="text-danger">Total Due: ৳ <span id="totalDue"></span> Tk</h5>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="date" class="form-label">Date</label>
              <input type="date" class="form-control" name="date" id="date" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="purchaseType" class="form-label">Purchase Type</label>
              <select id="purchaseType" class="form-select" name="purchase_type" required>
                <!-- Options will be appended dynamically -->
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="payment_type" class="form-label">Payment Type</label>
              <select id="payment_type" class="form-select" name="payment_type">
                <option value="cash_payment">Cash Payment</option>
                <option value="check_payment">Check Payment</option>
                <option value="online_transaction">Online Transaction</option>
                <option value="mobile_banking">Mobile Banking</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="amount" class="form-label">Paid Amount</label>
              <input type="text" name="due_amount" min="1" class="form-control" id="due_amount" hidden>
              <input type="text" name="paid_amount" min="1" class="form-control" id="paid_amount" required>
            </div>
          </div>
          <!-- 🏦 Bank-Related Fields -->
          <div class="row bank-info" style="display: none;">
            <div class="col-md-6 mb-3" id="bank_name">
              <label for="bank_name" class="form-label">Bank Name</label>
              <input type="text" name="bank_name" class="form-control" id="bank_name">
            </div>
            <div class="col-md-6 mb-3" id="bank_branch_name">
              <label for="bank_branch_name" class="form-label">Branch Name</label>
              <input type="text" name="bank_branch_name" class="form-control" id="bank_branch_name">
            </div>
          </div>
          <div class="bank-field" style="display: none;">
            <div class="row">
              <div class="col-md-4 mb-3" id="bank_cheque_number">
                <label for="bank_cheque_number" class="form-label">Bank Cheque Number</label>
                <input type="text" name="bank_cheque_number" min="1" class="form-control" id="bank_cheque_number">
              </div>
              <div class="col-md-4 mb-3" id="bank_account_number">
                <label for="bank_account_number" class="form-label">Bank Account Number</label>
                <input type="text" name="bank_account_number" min="1" class="form-control" id="bank_account_number">
              </div>
              <div class="col-md-4 mb-3">
                <label for="bank_micr_code" class="form-label">Bank MICR Code</label>
                <input type="text" name="bank_micr_code" class="form-control" id="bank_micr_code">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="bank_check_issue_date" class="form-label">Check Issue Date</label>
                <input type="date" name="bank_check_issue_date" class="form-control" id="bank_check_issue_date">
              </div>
              <div class="col-md-6 mb-3">
                <label for="bank_check_cleared_at" class="form-label">Check Cleared Date</label>
                <input type="date" name="bank_check_cleared_at" class="form-control" id="bank_check_cleared_at">
              </div>
            </div>
          </div>

          <!-- 🌐 Online Transfer Fields -->
          <div class="online-field" style="display: none;">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="sender_account_number" class="form-label">Sender Account Number</label>
                <input type="text" name="sender_account_number" class="form-control" id="sender_account_number">
              </div>
              <div class="col-md-6 mb-3">
                <label for="receiver_account_number" class="form-label">Receiver Account Number</label>
                <input type="text" name="receiver_account_number" class="form-control" id="receiver_account_number">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="online_transaction_id" class="form-label">Online Transaction ID</label>
                <input type="text" name="online_transaction_id" class="form-control" id="online_transaction_id">
              </div>
              <div class="col-md-6 mb-3">
                <label for="online_transfer_method" class="form-label">Online Transfer Method</label>
                <select name="online_transfer_method" class="form-select" id="online_transfer_method">
                  <option value="">Select Online Transfer Method</option>
                  <option value="iBanking">iBanking</option>
                  <option value="ATM Transfer">ATM</option>
                  <option value="NPSB">NPSB</option>
                  <option value="BEFTN">BEFTN</option>
                  <option value="Mobile App">Mobile App</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
          </div>

          <!-- 💳 Card Payment Fields -->
          <!-- <div class="card-field" style="display: none;">
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Card Number</label>
                            <input type="text" name="card_number" class="form-control" id="card_number">
                        </div>
                        <div class="mb-3">
                            <label for="card_type" class="form-label">Card Type</label>
                            <input type="text" name="card_type" class="form-control" id="card_type" placeholder="e.g., Visa, MasterCard">
                        </div>
                        <div class="mb-3">
                            <label for="last_four_digits" class="form-label">Last Four Digits</label>
                            <input type="text" name="last_four_digits" class="form-control" id="last_four_digits" maxlength="4">
                        </div>
                        <div class="mb-3">
                            <label for="card_expiry_date" class="form-label">Card Expiry Date</label>
                            <input type="month" name="card_expiry_date" class="form-control" id="card_expiry_date">
                        </div>
                        <div class="mb-3">
                            <label for="card_cvv" class="form-label">Card CVV</label>
                            <input type="password" name="card_cvv" class="form-control" id="card_cvv" maxlength="4">
                        </div>
                        <div class="mb-3">
                            <label for="card_image" class="form-label">Card Image</label>
                            <input type="file" name="card_image" class="form-control" id="card_image" accept="image/*">
                        </div>
                    </div> -->

          <!-- 📱 Mobile Banking Fields -->
          <div class="mobile-field" style="display: none;">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="mobile_banking_type" class="form-label">Mobile Banking Type</label>
                <select name="mobile_banking_type" class="form-select" id="mobile_banking_type">
                  <option value="">Select Mobile Banking</option>
                  <option value="bKash">bKash</option>
                  <option value="Nagad">Nagad</option>
                  <option value="Rocket">Rocket</option>
                  <option value="Upay">Upay</option>
                  <option value="SureCash">SureCash</option>
                  <option value="Tap">Tap</option>
                  <option value="mCash">mCash</option>
                  <option value="FirstCash">FirstCash</option>
                  <option value="UCash">UCash</option>
                  <option value="OK Wallet">OK Wallet</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="mobile_banking_account_type" class="form-label">Account Type</label>
                <select name="mobile_banking_account_type" class="form-select" id="mobile_banking_account_type">
                  <option value="">Select Account Type</option>
                  <option value="Personal">Personal</option>
                  <option value="Agent">Agent</option>
                  <option value="Merchant">Merchant</option>
                </select>
              </div>

            </div>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="mobile_banking_sender_number" class="form-label">Sender Number</label>
                <input type="text" name="mobile_banking_sender_number" class="form-control" id="mobile_banking_sender_number">
              </div>
              <div class="col-md-4 mb-3">
                <label for="mobile_banking_receiver_number" class="form-label">Receiver Number</label>
                <input type="text" name="mobile_banking_receiver_number" class="form-control" id="mobile_banking_receiver_number">
              </div>
              <div class="col-md-4 mb-3">
                <label for="mobile_banking_transaction_id" class="form-label">Transaction ID</label>
                <input type="text" name="mobile_banking_transaction_id" class="form-control" id="mobile_banking_transaction_id">
              </div>
            </div>
            <!-- <div class="mb-3">
                            <label for="mobile_banking_image" class="form-label">Screenshot / Receipt</label>
                            <input type="file" name="mobile_banking_image" class="form-control" id="mobile_banking_image" accept="image/*">
                        </div> -->
          </div>

          <div class="row">
            <!-- Status -->
            <div class="col-md-4 mb-3">
              <label for="status" class="form-label">Payment Status</label>
              <select name="status" class="form-select" id="">
                <option value="paid">Paid</option>
                <option value="processing">Processing</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
              </select>
            </div>
            <div class="col-md-4 mb-3" id="payee_name">
              <label for="payee_name" class="form-label">Paid By</label>
              </table>
              <input type="text" name="payee_name" class="form-control" id="payee_name" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="received_by" class="form-label">Received By</label>
              <input type="text" name="received_by" min="1" class="form-control" id="received_by" value="{{Auth::user()->name}}">
            </div>
          </div>
          <div class="mb-3">
            <label for="remarks" class="form-label">Remark</label>
            <textarea class="form-control" name="remarks" id="remarks" rows="2"></textarea>
          </div>

          <button type="submit" class="col-md-12 btn btn-success">Make Payment</button>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- make payment script -->
<script>
  function makePayment(id, supplierMobileNumber, supplierName, totalDue) {
    // Populate the modal with customer name and total due amount
    document.getElementById('supplierName').textContent = supplierName;
    document.getElementById('supplierMobileNumber').textContent = supplierMobileNumber;
    document.getElementById('supplierId').value = id;
    document.getElementById('totalDue').textContent = totalDue;
    document.getElementById('due_amount').value = totalDue;

    const purchase_select = document.getElementById('purchaseType');

    // Clear options and re-add default
    purchase_select.innerHTML = '<option value="all">All Purchase</option>';
    // console.log(id);

    // Fetch and populate invoices
    fetch(`/supplier/get-supplier-purchases/${id}`)
      .then(response => response.json())
      .then(data => {
        data.forEach(purchase => {
          const option = document.createElement('option');
          option.value = purchase.purchase_id;
          option.textContent = `#${purchase.purchase_id} - ${purchase.due_amount} Tk`;
          purchase_select.appendChild(option);
        });

        // Reinitialize select2 after options are added
        $('#purchaseType').select2({
          dropdownParent: $('#staticBackdrop'),
          placeholder: "Select Purchase",
          allowClear: true,
          width: '100%'
        });
      });
    $(document).on('change', '#purchaseType', function() {
      const selectedText = $('#purchaseType option:selected').text();
      const paid_mount = selectedText.split('-')[1]?.trim().replace('Tk', '').replace(' ', '') || '';
      // alert(dueAmount);
      document.getElementById('paid_amount').value = paid_mount;
    });
    $(document).on('change', '#payment_type', function() {
      const selected = $(this).val();

      // Hide all fields first
      $('.bank-field, .online-field, .mobile-field,.bank-info').hide();

      // Show the relevant field based on the selection
      if (selected === 'check_payment') {
        $('.bank-info').show();
        $('.bank-field').show();
      } else if (selected === 'online_transaction') {
        $('.bank-info').show();
        $('.online-field').show();
      } else if (selected === 'mobile_banking') {
        $('.mobile-field').show();
      }
    });

    $('#staticBackdrop').on('shown.bs.modal', function() {
      $('#payment_type').trigger('change');
    });
    // Show the modal
    var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
    myModal.show();
  }
</script>
<!-- purchase and transaction btn script -->
<script>
  function showTable(tableType) {
    const purchaseWrapper = document.getElementById('purchaseTableWrapper');
    const transactionWrapper = document.getElementById('transactionTableWrapper');

    const purchaseTable = purchaseWrapper.querySelector('table');
    const transactionTable = transactionWrapper.querySelector('table');

    // Remove existing datatable instance if any
    if ($.fn.DataTable.isDataTable('#datatable')) {
      $('#datatable').DataTable().destroy();
    }

    // Remove IDs
    purchaseTable.removeAttribute('id');
    transactionTable.removeAttribute('id');

    // Hide both
    purchaseWrapper.style.display = 'none';
    transactionWrapper.style.display = 'none';

    // Show selected and assign datatable ID
    if (tableType === 'purchase') {
      purchaseWrapper.style.display = 'block';
      purchaseTable.setAttribute('id', 'datatable');
    } else {
      transactionWrapper.style.display = 'block';
      transactionTable.setAttribute('id', 'datatable');
    }

    // Reinitialize datatable
    setTimeout(() => {
      $('#datatable').DataTable();
    }, 10);
  }

  // Initialize datatable on default purchase table
  $(document).ready(function() {
    $('#datatable').DataTable();
  });
</script>


@endsection