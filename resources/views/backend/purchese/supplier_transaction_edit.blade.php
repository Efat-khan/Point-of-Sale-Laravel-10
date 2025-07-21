@extends('admin.admin_master')
@section('admin')
<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
          <h4 class="mb-sm-0">EDIT TRANSACTION</h4>
          <div class="page-title-right">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
              <li class="m-2 breadcrumb-item"><a href="{{route('purchase.supplier.all.transaction')}}"> BACK </a></li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <form action="{{route('purchase.supplier.transaction.update')}}" method="POST" autocomplete="off">
              @csrf
              <input type="text" name="supplier_id" id="supplierId" value="{{$common['supplier_id']}}" hidden>
              <input type="text" name="transaction_id" value="{{$common['transaction_id']}}" hidden>
              <div class="mb-3">
                <h4 id="supplierName">{{ $payments[0]->supplier->name }}</h4>
                <p>Mobile Number: {{$payments[0]->supplier->mobile_no}}</p>
                <!-- <h5 class="text-danger">Total Due: ৳ <span id="totalDue"></span> Tk</h5> -->
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="date" class="form-label">Date</label>
                  <input type="date" class="form-control" name="date" id="date" value="{{\Carbon\Carbon::parse($common['date'])->format('Y-m-d')}}" required readonly>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="purchase_type" class="form-label">Purchase</label>
                  <input type="text" class="form-control" name="purchase_type" id="purchase_type" value="{{$purchase_ids_string}}" readonly>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="payment_type" class="form-label">Payment Type</label>
                  <select id="payment_type" class="form-select" name="payment_type">
                    <option value="cash_payment" {{ $common['payment_type'] == 'check_payment'? 'selected':'' }}>Cash Payment</option>
                    <option value="check_payment" {{ $common['payment_type'] == 'check_payment'? 'selected':'' }}>Check Payment</option>
                    <option value="online_transaction" {{ $common['payment_type'] == 'online_transaction'? 'selected':'' }}>Online Transaction</option>
                    <option value="mobile_banking" {{ $common['payment_type'] == 'mobile_banking'? 'selected':'' }}>Mobile Banking</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="amount" class="form-label">Paid Amount</label>
                  <input type="text" name="paid_amount" min="1" class="form-control" id="paid_amount" value="{{$total_paid}}" required readonly>
                </div>
              </div>
              <!-- 🏦 Bank-Related Fields -->
              @php
              $showBankInfo = in_array($common['payment_type'] ?? '', ['check_payment', 'online_transaction']);
              @endphp
              <div class="row bank-info {{ $showBankInfo ? 'd-flex' : '' }}" style="display: {{ $showBankInfo ? 'block' : 'none' }};">
                <div class="col-md-6 mb-3">
                  <label for="input_bank_name" class="form-label">Bank Name</label>
                  <input type="text" name="bank_name" class="form-control" id="input_bank_name" value="{{ $common['bank_name'] ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="input_bank_branch_name" class="form-label">Branch Name</label>
                  <input type="text" name="bank_branch_name" class="form-control" id="input_bank_branch_name" value="{{ $common['bank_branch_name'] ?? '' }}">
                </div>
              </div>

              <div class="bank-field" style="display: {{ in_array($common['payment_type'], ['check_payment']) ? 'block' : 'none' }};">
                <div class="row">
                  <div class="col-md-4 mb-3" id="bank_cheque_number">
                    <label for="bank_cheque_number" class="form-label">Bank Cheque Number</label>
                    <input type="text" name="bank_cheque_number" min="1" class="form-control" id="bank_cheque_number" value="{{$common['bank_cheque_number']??''}}">
                  </div>
                  <div class="col-md-4 mb-3" id="bank_account_number">
                    <label for="bank_account_number" class="form-label">Bank Account Number</label>
                    <input type="text" name="bank_account_number" min="1" class="form-control" id="bank_account_number" value="{{$common['bank_account_number']??''}}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="bank_micr_code" class="form-label">Bank MICR Code</label>
                    <input type="text" name="bank_micr_code" class="form-control" id="bank_micr_code" value="{{$common['bank_micr_code']??''}}">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="bank_check_issue_date" class="form-label">Check Issue Date</label>
                    <input type="date" name="bank_check_issue_date" class="form-control" id="bank_check_issue_date" value="{{$common['bank_check_issue_date']??''}}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="bank_check_cleared_at" class="form-label">Check Cleared Date</label>
                    <input type="date" name="bank_check_cleared_at" class="form-control" id="bank_check_cleared_at" value="{{$common['bank_check_cleared_at']??''}}">
                  </div>
                </div>
              </div>

              <!-- 🌐 Online Transfer Fields -->
              <div class="online-field" style="display: {{ in_array($common['payment_type'], ['online_transaction']) ? 'block' : 'none' }};">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="sender_account_number" class="form-label">Sender Account Number</label>
                    <input type="text" name="sender_account_number" class="form-control" id="sender_account_number" value="{{$common['sender_account_number']??''}}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="receiver_account_number" class="form-label">Receiver Account Number</label>
                    <input type="text" name="receiver_account_number" class="form-control" id="receiver_account_number" value="{{$common['receiver_account_number']??''}}">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="online_transaction_id" class="form-label">Online Transaction ID</label>
                    <input type="text" name="online_transaction_id" class="form-control" id="online_transaction_id" value="{{$common['online_transaction_id']??''}}">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="online_transfer_method" class="form-label">Online Transfer Method</label>
                    <select name="online_transfer_method" class="form-select" id="online_transfer_method">
                      <option value="">Select Online Transfer Method</option>
                      <option value="iBanking" {{ $common['online_transfer_method'] == 'iBanking'? 'selected':'' }}>iBanking</option>
                      <option value="ATM Transfer" {{ $common['online_transfer_method'] == 'ATM Transfer'? 'selected':'' }}>ATM</option>
                      <option value="NPSB" {{ $common['online_transfer_method'] == 'NPSB'? 'selected':'' }}>NPSB</option>
                      <option value="BEFTN" {{ $common['online_transfer_method'] == 'BEFTN'? 'selected':'' }}>BEFTN</option>
                      <option value="Mobile App" {{ $common['online_transfer_method'] == 'Mobile App'? 'selected':'' }}>Mobile App</option>
                      <option value="Other" {{ $common['online_transfer_method'] == 'Other'? 'selected':'' }}>Other</option>
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
              <div class="mobile-field" style="display: {{ in_array($common['payment_type'], ['mobile_banking']) ? 'block' : 'none' }};">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="mobile_banking_type" class="form-label">Mobile Banking Type</label>
                    <select name="mobile_banking_type" class="form-select" id="mobile_banking_type">
                      <option value="">Select Mobile Banking</option>
                      <option value="bKash" {{ $common['mobile_banking_type'] == 'bKash'? 'selected':'' }}>bKash</option>
                      <option value="Nagad" {{ $common['mobile_banking_type'] == 'Nagad'? 'selected':'' }}>Nagad</option>
                      <option value="Rocket" {{ $common['mobile_banking_type'] == 'Rocket'? 'selected':'' }}>Rocket</option>
                      <option value="Upay" {{ $common['mobile_banking_type'] == 'Upay'? 'selected':'' }}>Upay</option>
                      <option value="SureCash" {{ $common['mobile_banking_type'] == 'SureCash'? 'selected':'' }}>SureCash</option>
                      <option value="Tap" {{ $common['mobile_banking_type'] == 'Tap'? 'selected':'' }}>Tap</option>
                      <option value="mCash" {{ $common['mobile_banking_type'] == 'mCash'? 'selected':'' }}>mCash</option>
                      <option value="FirstCash" {{ $common['mobile_banking_type'] == 'FirstCash'? 'selected':'' }}>FirstCash</option>
                      <option value="UCash" {{ $common['mobile_banking_type'] == 'UCash'? 'selected':'' }}>UCash</option>
                      <option value="OK Wallet" {{ $common['mobile_banking_type'] == 'OK Walletther'? 'selected':'' }}>OK Wallet</option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="mobile_banking_account_type" class="form-label">Account Type</label>
                    <select name="mobile_banking_account_type" class="form-select" id="mobile_banking_account_type">
                      <option value="">Select Account Type</option>
                      <option value="Personal" {{ $common['mobile_banking_account_type'] == 'Personal'? 'selected':'' }}>Personal</option>
                      <option value="Agent" {{ $common['mobile_banking_account_type'] == 'Agent'? 'selected':'' }}>Agent</option>
                      <option value="Merchant" {{ $common['mobile_banking_account_type'] == 'Merchant'? 'selected':'' }}>Merchant</option>
                    </select>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label for="mobile_banking_sender_number" class="form-label">Sender Number</label>
                    <input type="text" name="mobile_banking_sender_number" class="form-control" id="mobile_banking_sender_number" value="{{$common['mobile_banking_sender_number']??''}}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="mobile_banking_receiver_number" class="form-label">Receiver Number</label>
                    <input type="text" name="mobile_banking_receiver_number" class="form-control" id="mobile_banking_receiver_number" value="{{$common['mobile_banking_receiver_number']??''}}">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="mobile_banking_transaction_id" class="form-label">Transaction ID</label>
                    <input type="text" name="mobile_banking_transaction_id" class="form-control" id="mobile_banking_transaction_id" value="{{$common['mobile_banking_transaction_id']??''}}">
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
                    <option value="paid" {{ $common['status'] == 'paid'? 'selected':'' }}>Paid</option>
                    <option value="processing" {{ $common['status'] == 'processing'? 'selected':'' }}>Processing</option>
                    <option value="failed" {{ $common['status'] == 'failed'? 'selected':'' }}>Failed</option>
                    <option value="refunded" {{ $common['status'] == 'refunded'? 'selected':'' }}>Refunded</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3" id="payee_name">
                  <label for="payee_name" class="form-label">Paid By</label>
                  </table>
                  <input type="text" name="payee_name" class="form-control" id="payee_name" value="{{$common['payee_name']??''}}" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="received_by" class="form-label">Received By</label>
                  <input type="text" name="received_by" min="1" class="form-control" id="received_by" value="{{$common['received_by']??''}}" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="remarks" class="form-label">Remark</label>
                <textarea class="form-control" name="remarks" id="remarks" rows="2">{{$common['remarks']??''}}</textarea>
              </div>
              <button type="submit" class="col-md-12 btn btn-success">Make Payment</button>
            </form>
          </div>
        </div>
      </div>
      @endsection
      @section('admin_custom_js')
      <script>
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
      </script>
      @endsection