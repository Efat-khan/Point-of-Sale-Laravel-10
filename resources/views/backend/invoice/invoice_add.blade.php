@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Add Invoice</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                            <li class="breadcrumb-item active"><a href="{{route('invoice.all')}}">Back</a></li>
                            <!-- <li class="breadcrumb-item active"><a href="{{route('print.invoice.list')}}">PRINTED INVOICE</a></li> -->
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-s-12">
                <div class="card">
                    <!-- <form method="post" action="{{ route('invoice.store') }}" onsubmit="return confirmAction(event)"> -->
                    <form method="post" action="{{ route('invoice.store') }}" id="postForm">
                        @csrf
                        <div class="card-body pb-0">
                            <div class="row ">
                                <div class="form-group col-md-3 ">
                                    <label for="example-text-input" class="col-form-label">Add Products (Scan Barcode)</label>
                                    <div>
                                        <!-- Barcode input START-->
                                        <input type="search" id="search-product-or-barcode-input" class="form-control" placeholder="Scan Barcode">
                                        <!-- Barcode input END-->
                                    </div>
                                </div>
                                <div class="form-group col-md-9 pb-0">
                                    <label for="example-text-input" class="col-form-label">Add Products </label>
                                    <select id="product_barcode" class="form-select">
                                        <option value="">Add Product</option>
                                        @foreach($products as $key => $item)
                                        <option value="{{ $item->product_code }}">{{ $item->name }}
                                            {{ $item->category_id !=0 ? "- {$item['category']['name']} - " : '' }}
                                            {{ $item->brand_id !=0 ? "({$item['brand']['name']})" : '' }}
                                        </option>
                                        @endforeach
                                    </select><br>
                                </div>
                                <div class="col-md-2 text-left">
                                    <label for="example-text-input" class="col-form-label">Invoice/ Challan/ DN No.</label>
                                    <input class="form-control" type="text" name="invoice_no" value="{{ $invoice_no }}" id="invoice_no" readonly style="background-color:#ddd; ">
                                </div>
                                <!-- <div class="col-md-2 text-left">
                                    <label for="example-text-input" class="col-form-label">.</label>
                                    <input class="form-control" type="text" name="dn_no" value="{{ $invoice_no }}" id="dn_no" readonly style="background-color:#ddd; ">
                                </div> -->
                                <div class="col-md-3 text-left">
                                    <label for="example-text-input" class="col-form-label">WO No.</label>
                                    <input class="form-control" type="text" name="wo_no" value="" id="wo_no" placeholder="Enter WO No">
                                </div>
                                <div class="form-group col-md-5 pb-0">
                                    <label for="example-text-input" class="col-form-label">Customer </label>
                                    <select name="customer_id" id="customer_id" class="form-select">
                                        <option value="-2">Select Customer</option>
                                        <!-- <option value="-1">পথচারি কাস্টমার</option> -->
                                        @foreach($customer as $cust)
                                        <option value="{{ $cust->id }}">{{ $cust->name }} - {{$cust->mobile_no }}</option>
                                        @endforeach
                                        <option value="0">+ New Customer</option>
                                    </select><br>
                                </div>
                                <div class="col-md-2">
                                    <label for="example-text-input" class="col-form-label" style="text-align: right;">Date:</label>
                                    <input class="form-control example-date-input" name="date" value="{{ $date }}" type="date" id="date">
                                </div>

                                <!-- <div class="form-group col-md-1" style="text-align: right; padding-top: 42px;">
                                    <button type="submit" class="btn btn-info sm fas fa-plus-circle addProduct"></button>
                                </div> -->
                            </div>

                            <!-- Hide New Customer insert form -->
                            <div class="row new_customer mb-0" style="display: none;">
                                <div class="form-group col-md-4">
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Customer Name">
                                </div>

                                <div class="form-group col-md-4">
                                    <input type="number" name="mobile_no" id="mobile_no" class="form-control" placeholder="Customer Mobile No">
                                </div>

                                <div class="form-group col-md-4">
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Customer Email">
                                </div>
                                <div class="form-group col-md-12 mt-2">
                                    <textarea name="address" id="address" class="form-control" placeholder="Write address Here..."></textarea>
                                </div>
                            </div>
                        </div> <!-- End card-body -->
                        <div class="card-body">
                            <table class="table-sm table-bordered" width="100%" style="border-color: #ddd;">
                                <thead>
                                    <tr>
                                        <th width="220px;">Product Name </th>
                                        <th width="60px;">PSC/Stock</th>
                                        <th width="170px;">Unit Price</th>
                                        <!-- <th width="170px;">Unit Price /P.Code</th> -->
                                        <th width="180px;">VAT</th>
                                        <th width="160px;">Discount(%)</th>
                                        <th>Price/VAT</th>
                                    </tr>
                                </thead>
                                <tbody id="addRow" class="addRow">

                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-end">
                                            <div class="form-group">
                                                <select name="discount_status" id="discount_status" class="form-select" style="float: right; width:31%">
                                                    <option value="fixed_discount">Fixed Discount</option>
                                                    <option value="percentage_discount">Percentage Discount(%)</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="discount_amount" id="discount_amount" class="form-control discount_amount" value="0" autocomplete="off">
                                            <input type="text" name="discount_show" id="discount_show" class="form-control discount_show" value="0" readonly>
                                        </td>
                                    <tr>
                                        <td colspan="5" class="text-end">
                                            Total Discount
                                        </td>
                                        <td>
                                            <input type="text" name="total_discount_amount" id="total_discount_amount" class="form-control total_discount_amount" value="0" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td colspan="1" class="text-start" >Total Buying Price Code</td>
                                        <td colspan="2" >
                                            <input type="text" name="secret_grand_total_price_code" value="0" id="secret_grand_total_price_code" class="form-control secret_grand_total_price_code" readonly style="background-color: #ddd;">
                                        </td> -->
                                        <td colspan="5" class="text-end"> Total</td>
                                        <td>
                                            <input type="text" name="estimated_amount" value="0" id="estimated_amount" class="form-control estimated_amount" readonly style="background-color: #ddd;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">VAT</td>
                                        <td>
                                            <select name="total_taxes[]" multiple="multiple" id="total_taxes" class="form-group form-select">
                                                <!-- <option value="TaxFree">VAT Free</option> -->
                                                @foreach ($tax as $key=>$value)
                                                <option value="{{$value->id}}" data-rate="{{$value->rate}}">{{$value->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="tax_value" id="tax_value" class="form-control discount_show" value="0" readonly>
                                            <input type="text" name="invoice_tax_amount" id="invoice_tax_amount" class="form-control " value="0" hidden>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">Additional Fee</td>
                                        <td>
                                            <select name="total_additional_fees_type[]" multiple="multiple" id="total_additional_fees_type" class="form-group form-select">
                                                <option value="noFee">No Fee</option>
                                                @foreach ($additional_fees as $key=>$value)
                                                <option value="{{$value->id}}" data-amount="{{$value->amount}}">{{$value->name }} </option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="total_additional_fees_amount" id="total_additional_fees_amount" class="form-control" value="0" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">Grand Total</td>
                                        <td>
                                            <input type="text" name="total" value="0" id="total" class="form-control total" readonly style="background-color: #ddd;">
                                        </td>
                                    </tr>
                                    <tr>
                                        {{-- 
                                            <td colspan="5" class="text-start">Total Profit Code</td>
                                            <td colspan="2">
                                                <input type="text" name="total_profit_code" value="0" id="total_profit_code" class="form-control total_profit_code" readonly style="background-color: #ddd;">
                                            </td>
                                        --}}
                                        <td colspan="5" class="text-end">
                                            <div class="form-group">
                                                <select name="paid_status" id="paid_status" class="form-select" style="float: right; width:30%">
                                                    <option value="full-due">Select Paid Status</option>
                                                    <option value="full-due">Full Due</option>
                                                    <option value="full-paid">Full Paid</option>
                                                    <option value="partial-paid">Partial Paid</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="paid_amount" class="form-control paid_amount" id="paid_amount" readonly placeholder="Paid" value="0" autocomplete="off">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end">Due</td>
                                        <td>
                                            <input type="text" name="due_amount" class="form-control due_amount" id="due_amount" style="background-color: #e7b5b5;" value="0" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table><br>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <textarea name="description" id="description" class="form-control" placeholder="Write Description Here..."></textarea>
                                </div>
                            </div> <br>
                            <!-- Hidden input to track which button was clicked -->
                            <input type="hidden" name="saveBtn" id="saveBtn" value="">
                            <div class="form-group">
                                <button type="button" class="btn btn-info previewBtn" data-value="3" data-action="draft" id="draftButton" disabled>
                                    <i class="fas fa-check-circle"></i> Draft
                                </button>
                                <button type="button" class="btn btn-primary previewBtn" data-value="1" data-action="quotation" id="quotationButton" disabled>
                                    <i class="fas fa-check-circle"></i> Quotation 
                                </button>
                                <button type="button" class="btn btn-primary previewBtn" data-value="4" data-action="challan" id="chalanButton" disabled>
                                    <i class="fas fa-check-circle"></i> Challan
                                </button>
                                <button type="button" class="btn btn-primary previewBtn" data-value="2" data-action="invoice" id="saveAndPrintPDFButton" disabled>
                                    <i class="fas fa-check-circle"></i> Invoice
                                </button>
                            </div>

                        </div> <!-- End card-body -->
                </div>
                </form>
            </div> <!-- end col -->
        </div>
    </div>
    <!-- Preview model start-->
    <div class="modal fade" id="invoicePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <iframe id="invoicePreviewIframe" width="100%" height="600px" style="border:none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Edit</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Confirm & Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Preview model end-->
    <!-- Product row insert in invoice -->
    <script id="document-template" type="text/x-handlebars-template">
        <tr class="delete_add_more_item" id="delete_add_more_item">
            <input type="hidden" name="date" value="@{{date}}">
            <input type="hidden" name="invoice_no" value="@{{invoice_no}}">
            <input type="hidden" name="category_id[]" value="@{{category_id}}">
            <input type="hidden" name="product_id[]" value="@{{product_id}}">
            <td>
                @{{ product_name }}-@{{brand_name}}(@{{tax_type}})
                
            </td>
            <td>
                <input type="number"  min="1" max="@{{quantity}}" class="form-control selling_qty text-right" name="selling_qty[]" id="selling_qty" value="0"> 
                <input type="text" class="form-control stock_quantity text-right" name="stock_quantity" value="@{{quantity}}" readonly> 
            </td>
            <td>
                <input type="text" class="form-control product_buying_price text-right" name="product_buying_price[]" value="@{{product_buying_price}}" hidden> 
                <input type="text" class="form-control unit_price text-right" name="unit_price[]" value="@{{product_price}}"> 
                <input type="text" class="form-control unit_price_code text-right" name="product_price_code" value="@{{product_price_code}}" hidden>
            </td>
            <!-- <td colspan="2">
                <input type="text" class="form-control unit_price_code text-right" name="product_price_code" value="@{{product_price_code}}" readonly> 
            </td> -->
            <!-- VAT  -->
            <td class="discount-row">
                        <select name="product_tax[@{{product_id}}][]" multiple="multiple" class="form-group form-select product_tax">
                            <option value="TaxFree">VAT Free</option>
                            @{{#each selected_tax}}
                                <option value="@{{this.id}}" data-rate="@{{this.rate}}" selected>@{{this.name}} </option>
                            @{{/each}}
                            @{{#each unselected_tax}}
                                <option value="@{{this.id}}" data-rate="@{{this.rate}}">@{{this.name}}</option>
                            @{{/each}}
                        </select>
                    </td>
            <!-- Discount  -->
            <td class="discount-row">
                    <div class="form-check form-switch">
                        <input class="form-check-input discount_per_product" type="checkbox">
                        <label class="form-check-label discount_label" for="flexSwitchCheckDefault">Fixed</label>
                    </div>
                    <input type="text" class="form-control discount_rate text-right" id="discount_rate" name="discount_rate[]" value="@{{product_discount}}" autocomplete="off">
                    <input type="text" class="form-control discount_amount_per_product text-right" id="discount_amount_per_product" name="discount_amount_per_product[]" readonly >
            </td>
            
        <td>
            <input type="text" class="form-control selling_price text-right" id="selling_price" name="selling_price[]" value="0" readonly>
            <input type="text" class="form-control buying_price text-right" id="buying_price" name="buying_price[]" value="0" hidden>
            <input type="text" class="form-control product_tax_amount text-right" id="" name="product_tax_amount[]" value="0" readonly>
            <input type="text" class="form-control product_price_for_tax text-right" id="" name="product_price_for_tax[]" value="0" readonly hidden>
        </td>

        <td style="width:fit-content;">
            <i class="btn btn-danger btn-sm fas fa-window-close removeeventmore"></i>
        </td>
        </tr>
    </script>

    <!-- Product row insert in invoice End-->

    <!--Select2 Start -->
    <script>
        function initializeSelect2() {
            $('.product_tax').select2({
                placeholder: "Select Tax",
                allowClear: true,
                width: '100%'
            });
        }
        $(document).ready(function() {
            // Initialize Select2
            $('#total_taxes').select2();
            initializeSelect2();
            $('#total_additional_fees_type').select2();
            // $('#customer_id').select2();

        });
    </script>
    <!-- Select2 End-->
    <!-- Show Confirm Msg -->
    <!-- <script>
        console.log('Confirm Action Script Loaded');

        function confirmAction(event) {
            console.log('Confirm Action Triggered');
            // Get the clicked button
            const button = event.submitter;
            const action = button.innerText.trim(); // Get button label text

            const confirmed = confirm(`Are you sure you want to perform "${action}"?`);
            if (!confirmed) {
                event.preventDefault(); // Stop form submission
                console.log('Action Cancelled');
                return false;
            }
            console.log('Action Confirmed');
            return true;
        }
    </script> -->

    <!-- Preview Script start-->
    <script>
        document.querySelectorAll('.previewBtn').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.dataset.value;
                document.getElementById('saveBtn').value = type;

                const form = document.getElementById('postForm');
                const formData = new FormData(form);
                console.log('Form Data:', formData); // Debugging line
                fetch("{{ route('invoice.preview') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: formData
                    })
                    .then(res => res.blob())
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        console.log('Preview URL:', url); // Debugging line
                        document.getElementById('invoicePreviewIframe').src = url;
                        const modal = new bootstrap.Modal(document.getElementById('invoicePreviewModal'));
                        modal.show();
                    });
            });
        });

        document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
            document.getElementById('postForm').submit();
        });
    </script>

    <!-- Preview Script end-->
    <!-- To enable Save and Save & Print invoice Btn START-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saveAndPrintPDFButton = document.getElementById('saveAndPrintPDFButton');
            const chalanButton = document.getElementById('chalanButton');
            const draftButton = document.getElementById('draftButton');
            const quotationButton = document.getElementById('quotationButton');
            const newCustomerForm = document.querySelector('.new_customer');

            function toggleUI(selectedValue) {
                // Default: disable everything and hide form
                saveAndPrintPDFButton.disabled = true;
                draftButton.disabled = true;
                quotationButton.disabled = true;
                chalanButton.disabled = true;
                newCustomerForm.style.display = 'none';

                // Enable buttons if valid customer is selected
                if (selectedValue !== '-2') {
                    saveAndPrintPDFButton.disabled = false;
                    draftButton.disabled = false;
                    quotationButton.disabled = false;
                    chalanButton.disabled = false;
                }

                const nameField = document.getElementById('name');
                const mobileField = document.getElementById('mobile_no');
                const emailField = document.getElementById('email');
                const addressField = document.getElementById('address');

                if (selectedValue === '0') {
                    newCustomerForm.style.display = 'flex'; // show new customer form
                    nameField.required = true;
                    mobileField.required = true;
                    emailField.required = true;
                    addressField.required = true;
                } else {
                    nameField.required = false;
                    mobileField.required = false;
                    emailField.required = false;
                    addressField.required = false;
                }
            }

            // Initialize Select2
            $(document).ready(function() {
                $('#customer_id').select2();

                // Trigger toggleUI on select2 change
                $('#customer_id').on('change', function() {
                    const selectedValue = $(this).val();
                    toggleUI(selectedValue);
                });
                // Call toggleUI initially
                toggleUI($('#customer_id').val());
            });
        });
    </script>

    <!-- To enable Save and Save & Print invoice Btn END-->
    <!-- Add Data Using Barcode and select START-->
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            let barcodeInput = document.getElementById("search-product-or-barcode-input");

            barcodeInput.addEventListener("keypress", function(event) {

                if (event.key === "Enter") { // Barcode scanner sends an "Enter" key after scanning
                    let barcode = barcodeInput.value.trim();
                    if (barcode !== "") {
                        fetchProductDetails(barcode);
                    }
                    barcodeInput.value = ""; // Clear input field
                }
            });
            $(document).ready(function() {
                $('#product_barcode').select2();
                // Trigger toggleUI on select2 change
                $('#product_barcode').on('change', function() {
                    const selectedValue = $(this).val();
                    fetchProductDetails(selectedValue);
                });
            });

            function fetchProductDetails(barcode) {
                var barcode = barcode;
                $.ajax({
                    url: "{{ route('get-product-by-barcode') }}",
                    type: "GET",
                    data: {
                        barcode: barcode
                    },
                    success: function(data) {
                        product_list = data.product;
                        brand_name = data.brand;
                        selected_tax = data.selected_tax || [];;
                        unselected_tax = data.unselected_tax || [];
                        var date = $('#date').val();
                        var invoice_no = $('#invoice_no').val();
                        var product_id = product_list.id;
                        var product_name = product_list.name;
                        var product_offer_price = product_list.product_offer_price;
                        var product_price = product_list.product_price;
                        var product_price_code = product_list.product_price_code;
                        var category_id = product_list.category_id;

                        var quantity = product_list.quantity;
                        var tax_type = product_list.tax_type;
                        var tax = product_list.tax;
                        var product_discount = product_list.product_discount;
                        var db_com = product_list.db_com;
                        var m_com = product_list.market_com;
                        var s_com = product_list.special_com;
                        if (product_offer_price != null && product_offer_price != '') {
                            product_price = product_offer_price;
                        }

                        if (quantity != 0) {
                            if (date == '') {
                                $.notify("Date is Required", {
                                    globalPosition: 'top right',
                                    className: 'error'
                                });
                                return false;
                            }
                            if (product_id == '') {
                                $.notify("Product Field is Required", {
                                    globalPosition: 'top right',
                                    className: 'error'
                                });
                                return false;
                            }
                            var source = $("#document-template").html();
                            var tamplate = Handlebars.compile(source);
                            var data = {
                                date: date,
                                invoice_no: invoice_no,
                                product_id: product_id,
                                product_name: product_name,
                                product_price: product_price ? product_price : 0,
                                product_buying_price: product_list.product_buying_price ? product_list.product_buying_price : 0,
                                product_price_code: product_price_code,
                                quantity: quantity,
                                tax_type: tax_type,
                                tax: tax * 100,
                                category_id: category_id,
                                brand_name: brand_name,
                                product_discount: product_discount,
                                db_com: db_com,
                                m_com: m_com,
                                s_com: s_com,
                                selected_tax: selected_tax,
                                unselected_tax: unselected_tax,
                            };
                            var html = tamplate(data);
                            $("#addRow").append(html);
                            $("#addPreviewRow").append(html);
                            $('#search-product-or-barcode-input').val('');
                            // Enable select2 product_tax 
                            initializeSelect2();
                        } else {
                            $.notify("Product is out of stock.", {
                                globalPosition: 'top right',
                                className: 'error'
                            });
                            return false;
                        }
                    }
                })
            }
        });
    </script>
    <!-- Add Data Using Barcode and select END-->
    <!-- All calculations Start-->
    <script>
        $(document).ready(function() {
            // Remove product from invoice
            $(document).on("click", ".removeeventmore", function() {
                $(this).closest(".delete_add_more_item").remove();
                updateInvoiceCalculations();
            });

            // Event listener for price, quantity, discount, and commission changes
            $(document).on('keyup click change', '.unit_price, .selling_qty, .discount_rate, .discount_per_product, .product_tax', function() {
                updateProductRow($(this).closest("tr"));
                updateInvoiceCalculations();
            });

            // Handle tax calculation updates
            $('#total_taxes, #estimated_amount, #total_additional_fees_type').on('change', updateInvoiceCalculations);

            // Handle additional fees update
            $('#total_additional_fees_type').on('change', additionalFeeCalculations);

            // Paid status change handler
            $(document).on('change', '#paid_status', function() {
                adjustPaidAmount();
                updateInvoiceCalculations();
            });

            // Discount status change handler
            $(document).on('change', '#discount_status', updateInvoiceCalculations);

            // General update triggers for key financial fields
            $(document).on('keyup', '#discount_amount, #paid_amount, #due_amount, #paid_status, .selling_qty, #discount_status', updateInvoiceCalculations);

            /**
             * Update individual product row calculations
             */
            function updateProductRow(row) {
                let unitPrice = parseFloat(row.find("input.unit_price").val()) || 0;
                let buyingPrice = parseFloat(row.find("input.product_buying_price").val()) || 0;
                let quantity = parseFloat(row.find("input.selling_qty").val()) || 0;
                let discount = parseFloat(row.find("input.discount_rate").val()) || 0;
                let isPercentageDiscount = row.find('.discount_per_product').is(':checked');
                let discountLabel = row.find('.discount_label');
                let totalDiscount = 0;

                // Calculate discount per product
                if (!isNaN(discount)) {
                    let baseValue = unitPrice * quantity;
                    if (isPercentageDiscount) {
                        discountLabel.text('% Percent');
                        totalDiscount = (discount / 100) * baseValue;
                    } else {
                        discountLabel.text('Fixed');
                        totalDiscount = discount;
                    }
                }

                // Set discount total
                row.find("input.discount_amount_per_product").val(totalDiscount.toFixed(2));

                // Calculate total price after discount
                let totalSellingPrice = (unitPrice * quantity) - totalDiscount;
                let totalBuyingPrice = buyingPrice * quantity;
                row.find("input.selling_price").val(totalSellingPrice.toFixed(2));
                row.find("input.buying_price").val(totalBuyingPrice.toFixed(2));

                // Calculate tax per product
                let totalTax = 0;
                row.find('select.product_tax option:selected').each(function() {
                    totalTax += parseFloat($(this).data('rate')) || 0;
                });

                row.find("input.product_price_for_tax").val((unitPrice * quantity).toFixed(2));
                row.find("input.product_tax_amount").val((totalTax * unitPrice * quantity).toFixed(2));
            }

            /**
             * Calculate total tax, additional fees, and update invoice amounts
             */
            function updateInvoiceCalculations() {
                calculateTax();
                additionalFeeCalculations();
                calculateTotalAmount();
                adjustPaidAmount();
            }

            /**
             * Calculate total tax based on selected options
             */
            function calculateTax() {
                let totalTaxRate = 0;
                $('#total_taxes option:selected').each(function() {
                    totalTaxRate += parseFloat($(this).data('rate')) || 0;
                });

                let productPriceSum = sumValues(".product_price_for_tax");
                let productTaxSum = sumValues(".product_tax_amount");

                let totalTaxValue = productTaxSum + (productPriceSum * totalTaxRate);
                $('#tax_value').val(totalTaxValue.toFixed(2));
                $('#invoice_tax_amount').val((productPriceSum * totalTaxRate).toFixed(2));
            }

            /**
             * Calculate additional fees from selected options
             */
            function additionalFeeCalculations() {
                let totalAdditionalFees = sumValues('#total_additional_fees_type option:selected', 'data-amount');
                $('#total_additional_fees_amount').val(totalAdditionalFees.toFixed(2));
            }

            /**
             * Calculate total invoice amount
             */
            function calculateTotalAmount() {
                let totalDiscount = sumValues(".discount_amount_per_product");
                let totalSellingPrice = sumValues(".selling_price");
                let totalBuyingPrice = sumValues(".buying_price");

                let discountAmount = parseFloat($('#discount_amount').val()) || 0;
                let discountStatus = $('#discount_status').val();

                if (discountAmount) {
                    if (discountStatus === 'percentage_discount') {
                        let discount = (discountAmount / 100) * totalSellingPrice;
                        totalSellingPrice -= discount;
                        totalDiscount += discount;
                        $('#discount_show').val(discount.toFixed(2));
                    } else {
                        let discount = (discountAmount * 100) / totalSellingPrice;
                        totalSellingPrice -= discountAmount;
                        totalDiscount += discountAmount;
                        $('#discount_show').val(discount.toFixed(2));
                    }
                }

                $('#total_discount_amount').val(totalDiscount.toFixed(2));
                $('#secret_grand_total_price_code').val(totalBuyingPrice.toFixed(0));
                total_buying_price_code_generator('secret_grand_total_price_code');
                $('#estimated_amount').val(totalSellingPrice.toFixed(2));

                let totalProfit = totalSellingPrice - totalBuyingPrice;
                $('#total_profit_code').val(totalProfit > 0 ? totalProfit.toFixed(0) : -1);
                total_buying_price_code_generator('total_profit_code');
                adjustPaidAmount();
            }

            /**
             * Adjust paid amount based on status
             */
            function adjustPaidAmount() {
                let paidStatus = $('#paid_status').val();
                if (paidStatus === 'full-due') {
                    $('#paid_amount').val(0).attr('readonly', true);
                } else if (paidStatus === 'full-paid') {
                    $('#paid_amount').val($('#total').val()).attr('readonly', true);
                } else {
                    $('#paid_amount').attr('readonly', false);
                }

                let paidAmount = parseFloat($('#paid_amount').val()) || 0;
                let dueAmount = (parseFloat($('#total').val()) || 0) - paidAmount;
                $('#due_amount').val(dueAmount.toFixed(2));

                let total = sumValues('#total_additional_fees_amount, #tax_value, #estimated_amount');
                $('#total').val(total.toFixed(2));
            }

            /**
             * Helper function to sum values from multiple elements
             * @param {string} selector - The selector for the elements to sum
             * @param {string} [attribute] - Optional attribute to extract value from (e.g., 'data-amount')
             * @returns {number} - The total sum
             */
            function sumValues(selector, attribute) {
                let sum = 0;
                $(selector).each(function() {
                    let value = attribute ? parseFloat($(this).attr(attribute)) : parseFloat($(this).val());
                    if (!isNaN(value)) sum += value;
                });
                return sum;
            }
        });
    </script>

    <!-- All calculations End-->

    <!-- Add Product END -->
    <!-- Loading Category as from Brand -->
    <script type="text/javascript">
        $(function() {
            $(document).on('change', '#brand_id', function() {
                var brand_id = $(this).val();
                $.ajax({
                    url: "{{ route('get-category-by-brand') }}",
                    type: "GET",
                    data: {
                        brand_id: brand_id
                    },
                    success: function(data) {
                        product_list = data;
                        var html = '<option value="">Select Brand</option>';
                        $.each(data, function(key, v) {
                            html += '<option value=" ' + v.id + ' "> ' + v.name + '</option>';
                        });
                        $('#category_id').html(html);
                    }
                })
            });
        });
    </script>
    <!-- Loading Product as from Category -->
    <script type="text/javascript">
        $(function() {
            $(document).on('change', '#category_id', function() {
                var category_id = $(this).val();
                $.ajax({
                    url: "{{ route('get-product') }}",
                    type: "GET",
                    data: {
                        category_id: category_id
                    },
                    success: function(data) {
                        product_list = data;
                        var html = '<option value="">Select Product</option>';
                        $.each(data, function(key, v) {
                            html += '<option value=" ' + v.id + ' "> ' + v.name + '</option>';
                        });
                        $('#product_id').html(html);
                    }
                })
            });
        });
    </script>

    <!-- Display Product wise stock  -->
    <script type="text/javascript">
        $(function() {
            $(document).on('change', '#product_id', function() {
                var product_id = $(this).val();
                $.ajax({
                    url: "{{ route('check-product-stock') }}",
                    type: "GET",
                    data: {
                        product_id: product_id
                    },
                    success: function(data) {
                        $('#current_stock_qty').val(data);
                    }
                });
            });
        });
    </script>

    <!-- New Customer Insert form select New customer Option -->
    <script type="text/javascript">
        $(document).on('change', '#customer_id', function() {
            var customer_id = $(this).val();
            if (customer_id == '0') {
                $('.new_customer').show();
            } else {
                $('.new_customer').hide();
            }
        });
    </script>
    <!-- Search Data Table -->
    <script type="text/javascript">
        const searchDataTable = () => {
            let filterData = document.getElementById('search-product-or-barcode-input').value.toUpperCase();
            let productDataTable = document.getElementById('productDataTable');
            let tr = productDataTable.getElementsByTagName('tr');
            for (var i = 0; i < tr.length; i++) {
                let td = tr[i].getElementsByTagName('td');
                if (td.length > 0) {
                    if (td.length > 0) { // Check if the row has any td elements
                        let productCode = td[0].textContent || td[0].innerText;
                        let productName = td[1].textContent || td[1].innerText;

                        if (productName.toUpperCase().indexOf(filterData) > -1 || productCode.toUpperCase().indexOf(filterData) > -1) {
                            tr[i].style.display = '';
                        } else {
                            tr[i].style.display = 'none';
                        }
                    }
                }

            }
        }
    </script>

    <!-- Convert of buying price to price code -->
    <script type="text/javascript">
        function total_buying_price_code_generator(id) {
            var buyingPrice = parseInt($('#' + id).val());
            // console.log(buyingPrice);
            var html = "";
            var numberDictionary = @json($productPriceCode -> pluck('code', 'number'));

            if (buyingPrice != '') {
                if (parseInt(buyingPrice) == -1) {
                    html = "LOSS";
                } else {
                    html = numberToCode(buyingPrice);
                }
            }

            function numberToCode(num) {
                var strNum = num.toString();
                var result = "";
                var i = 0;

                while (i < strNum.length) {
                    // Check for the "000" pattern
                    if (i + 2 < strNum.length && strNum.substring(i, i + 3) === "000" && numberDictionary["000"]) {
                        result += numberDictionary["000"];
                        i += 3;
                    }
                    // Check for the "00" pattern
                    else if (i + 1 < strNum.length && strNum.substring(i, i + 2) === "00" && numberDictionary["00"]) {
                        result += numberDictionary["00"];
                        i += 2;
                    }
                    // Convert individual digits
                    else {
                        result += numberDictionary[strNum[i]];
                        i++;
                    }
                }
                return result.toUpperCase();
            }

            $("#" + id).val(html);
        }
    </script>
    @endsection