@extends('admin.admin_master')
@section('admin')


<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Supplier All Due & Payment 
                        {{-- <a href="{{ route('purchase.add') }}" class="btn btn-dark btn-rounded waves-effect waves-light"><i class="fas fa-plus-circle"> </i> ADD </a> --}}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                            @can('purchase-create')
                            <li class="breadcrumb-item active"><a href="{{route('purchase.add')}}">Add Purchase</a></li>
                            @endcan
                            <li class="breadcrumb-item active"><a href="{{route('purchase.all')}}">Back</a></li>
                            <!-- <li class="breadcrumb-item active"><a href="{{route('purchase.supplier_wise_purchese_payment.all')}}">SUPPLIER ALL DUE & PAYMENT</a></li> -->
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
                        <!-- <a href="{{route('credit.customer.print.pdf')}}" class="btn btn-dark btn-rounded waves-effect waves-light" style="float:right;"><i class="fas fa-print"> Print Credit Customer </i> </a> <br> <br> -->
                        <!-- <h4 class="card-title">Credit Customer All Data </h4> -->
                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20px;">#</th>
                                    <th>Supplier Name</th>
                                    <th>Supplier Phone</th>
                                    <th>Total Amount</th>
                                    <th>Total Due</th>
                                    <th width="20px;">Action</th>
                            </thead>
                            <tbody>
                                @foreach($supplier_purchase_due_and_payment_infos as $key => $info)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ !empty($info->name)?$info->name:'N/A' }} </td>
                                    <td> {{ !empty($info->mobile_no)?$info->mobile_no:''}} </td>
                                    <td> ৳ {{ !empty($info->total_amount)?number_format( $info->total_amount,2):'0.00'}} Tk</td>
                                    <td class="text-danger"> ৳ {{ !empty($info->total_due)?number_format( $info->total_due,2):'0.00'}} Tk</td>

                                    <td>
                                        <form action="{{route('purchase.wise.due.report')}}" method="GET" id="myForm">
                                            <button value="{{$info->id}}" name="supplier_id" class="btn btn-info sm" target="_blank" title="Purchase Details"> <i class="fas fa-eye"></i> </button>
                                            @can('supplier-transaction-create')
                                            <button type="button" class="btn btn-success" data-bs-target="#staticBackdrop" onclick="makePayment('{{$info->id}}','{{ $info->mobile_no}}','{{ $info->name }}', '{{ $info->total_due }}')"><i class="fa fa-credit-card"></i> Make Payment</button>
                                            @endcan
                                        </form>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- container-fluid -->
</div>
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
@endsection