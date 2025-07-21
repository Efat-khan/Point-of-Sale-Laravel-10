@extends('admin.admin_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Customer All Due & Payment</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            @can('customer-create')
                            <li class="m-2 breadcrumb-item"><a href="{{route('customer.add')}}"> ADD CUSTOMER </a></li>
                            @endcan
                            @can('customer-list')
                            <li class="m-2 breadcrumb-item"><a href="{{route('customer.all')}}"> ALL CUSTOMER </a></li>
                            @endcan
                            @can('customer-transaction-list')
                            <li class="m-2 breadcrumb-item"><a href="{{route('customer.all.transaction')}}"> CUSTOMERS TRANSACTIONS</a></li>
                            @endcan
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
                                    <th>Customer Name</th>
                                    <th>Customer Phone</th>
                                    <th>Total Amount</th>
                                    <th>Total Due</th>
                                    <th width="30px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer_due_and_payment_infos as $key => $info)
                                <tr>
                                    <td> {{ $key+1}} </td>
                                    <td> {{ !empty($info->name)?$info->name:'N/A' }} </td>

                                    <td> {{ !empty($info->mobile_no)?$info->mobile_no:''}} </td>
                                    <td> ৳ {{ !empty($info->total_amount)?number_format( $info->total_amount,2):'0.00'}} Tk</td>
                                    <td class="text-danger"> ৳ {{ !empty($info->total_due)?number_format( $info->total_due,2):'0.00'}} Tk</td>
                                    <td>
                                        <form action="{{route('customer.report')}}" method="GET" id="myForm">
                                            <button value="{{$info->id}}" name="customer_id" class="btn btn-info" target="_blank" title="Customer Invoice Details"> <i class="fas fa-eye"></i> </button>
                                            @can('customer-transaction-create')
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
                <form action="{{route('customer.due_payment.make_payment')}}" method="POST" autocomplete="off">
                    @csrf
                    <input type="text" name="customer_id" id="customerId" hidden>
                    <div class="mb-3">
                        <h4 id="customerName"></h4>
                        <p>Mobile Number: <span id="customer_mobile_number"></span></p>
                        <h5 class="text-danger">Total Due: ৳ <span id="totalDue"></span> Tk</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" id="date" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="invoice_type" class="form-label">Invoice Type</label>
                            <select id="invoiceType" class="form-select" name="invoice_type" required>
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
                                <option value="pending">Pending</option>
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
    function makePayment(id, customerMobileNumber, customerName, totalDue) {
        // Populate the modal with customer name and total due amount
        document.getElementById('customerName').textContent = customerName;
        document.getElementById('customer_mobile_number').textContent = customerMobileNumber;
        document.getElementById('customerId').value = id;
        document.getElementById('totalDue').textContent = totalDue;
        document.getElementById('due_amount').value = totalDue;

        const invoiceSelect = document.getElementById('invoiceType');

        // Clear options and re-add default
        invoiceSelect.innerHTML = '<option value="all">All Invoice</option>';

        // Fetch and populate invoices
        fetch(`/customer/get-customer-invoices/${id}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(invoice => {
                    const option = document.createElement('option');
                    option.value = invoice.invoice_id;
                    option.textContent = `#${invoice.invoice_id} - ${invoice.due_amount} Tk`;
                    invoiceSelect.appendChild(option);
                });

                // Reinitialize select2 after options are added
                $('#invoiceType').select2({
                    dropdownParent: $('#staticBackdrop'),
                    placeholder: "Select Invoice",
                    allowClear: true,
                    width: '100%'
                });
            });
        $(document).on('change', '#invoiceType', function() {
            const selectedText = $('#invoiceType option:selected').text();
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