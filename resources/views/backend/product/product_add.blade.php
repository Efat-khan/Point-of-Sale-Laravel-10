@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="page-content">
    <!--Start Page Title-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Add Product</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="breadcrumb-item m-2 "><a href="{{route('product.all')}}">ALL PRODUCT</a></li>
                            <li class="breadcrumb-item m-2 "><a href="{{route('productpricecode.all')}}">PRODUCT PRICE CODE</a></li>
                            <li class="breadcrumb-item m-2 "><a href="{{route('productLabelsprint.index')}}">PRINT PRODUCT LABELS</a></li>
                            <li class="breadcrumb-item m-2 "><a href="{{route('brand.add')}}">ALL BRAND</a></li>
                            <li class="breadcrumb-item m-2 "><a href="{{route('category.add')}}">ALL CATEGORY</a></li>
                            <li class="breadcrumb-item m-2 "><a href="{{route('unit.all')}}">ALL UNIT</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <form method="POST" action="{{ route('product.store')}}" id="myForm" enctype="multipart/form-data">
                @csrf
                <div class="col-12">
                    <div class="card">
                        <div class="card-body" style="font-size: .8rem;">

                            <div class="row mb-3">
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-5 col-form-label">Name</label>
                                    <input name="name" id="productName" class="form-control" type="text" placeholder="Product Name" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-5 col-form-label">Sort Name</label>
                                    <input name="product_sort_name" class="form-control" type="text" placeholder="Product Sort Name">
                                </div>
                                <!-- Brand Dropdown button  -->
                                <div class="form-group col-sm-3" id="brand_col">
                                    <label class="col-sm-12 col-form-label">Brand</label>
                                    <select class="form-select " name="brand_id" id="brand_id" aria-label="Default select example" required>
                                        <option selected value="0">Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                        @endforeach
                                        <option value="-1">+ Add</option>
                                    </select>
                                </div>
                                <!-- Hide New Brand insert form -->
                                <div class="new_brand col-sm-2" style="display: none;">
                                    <div class="form-group col-md-12">
                                        <label for="example-text-input" class="col-sm-5 col-form-label">+ Add Brand</label>
                                        <input type="text" name="brand_name" id="brand_name" class="form-control" placeholder="New Brand Name" required>
                                    </div>
                                </div> <br>
                                <!-- Brand button End Row -->
                                <!-- Category Dropdown button  -->
                                <div class="form-group col-sm-3" id="category_col">
                                    <label class="col-sm-12 col-form-label">Category </label>
                                    <select class="form-select" name="category_id" id="category_id" aria-label="Default select example" required>
                                        <option selected value="0">Select Brand</option>

                                    </select>
                                </div>
                                <!-- Category button End Row -->
                                <!-- Hide New Category insert form -->
                                <div class="new_category col-sm-2" style="display: none;">
                                    <div class="form-group col-md-12">
                                        <label for="example-text-input" class="col-sm-12 col-form-label">+ Add Category</label>
                                        <input type="text" name="category_name" id="category_name" class="form-control" placeholder="New Category Name" required>
                                    </div>
                                </div> <br>
                                <!-- Category button End Row -->
                            </div>
                            <!-- end row -->

                            <!-- supplier Dropdown button  -->
                            <!-- <div class="row mb-3">
                                <label class="form-group col-sm-2 col-form-label">Supplier Name</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="supplier_id" aria-label="Default select example">
                                        <option selected="">Select Supplier</option>
                                        @foreach($supplier as $supp)
                                        <option value="{{$supp->id}}">{{$supp->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->
                            <!-- Dropdown button End Row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="part_number" class="col-sm-12 col-form-label">Part Number</label>
                                        <input name="part_number" class="form-control" id="part_number" type="text" placeholder="Part Number" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="model_number" class="col-sm-12 col-form-label">Model Number</label>
                                        <input name="model_number" class="form-control" id="model_number" type="text" placeholder="Model Number" />
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <!-- Units Dropdown button  -->
                                <div class="col-sm-2" id="unit_col">
                                    <label class="col-sm-6 col-form-label">Unit </label>
                                    <select class="form-group form-select" name="unit_id" id="unit_id" aria-label="Default select example">
                                        <!-- <option selected value="0">Select Unit</option> -->
                                        @foreach($unit as $uni)
                                        <option value="{{$uni->id}}">{{$uni->name}}</option>
                                        @endforeach
                                        <option value="-1">+ Add</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-1" id="new_unit" style="display: none;">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">+Add Unit</label>
                                    <input name="unit_name" id="unit_name" class="form-control" type="text" autocomplete="off" placeholder="New Unit" required>
                                </div>

                                <div class="form-group col-sm-4" id="tax_type_btn">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">VAT Type</label>
                                    <select name="product_taxes[]" multiple="multiple" id="tax_type" class="form-group form-select">
                                        <option value="TaxFree">VAT Free</option>
                                        @foreach ($tax as $key=>$value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- <div class="form-group col-sm-2 div-tax" style="display: none;">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Tax(%) [e.g : 5%]</label>
                                    <input name="tax" id="tax" class="form-control" type="number" autocomplete="off" value="0">
                                </div> -->
                                <div class="form-group col-sm-2">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">SKU</label>
                                    <input name="sku" id="sku" class="form-control" type="text" autocomplete="off">
                                </div>
                                <!-- Product Code -->
                                <div class="form-group col-sm-2">
                                    <label for="example-text-input" class=" col-form-label">Product Code</label>
                                    <label id="productCode" class="btn btn-info btn-sm btn-rounded waves-effect waves-light">Generate</label>
                                    <input name="product_code" id="product_code" class="form-control" type="text" readonly autocomplete="off">
                                </div>
                                <!-- Product Code End -->

                                <!-- Units button End Row -->
                                <!-- Quantity -->
                                <div class="form-group col-sm-2">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Product Current Stock</label>
                                    <input name="quantity" class="form-control" type="number" autocomplete="off" placeholder="Product Current Stock" required>
                                </div>

                            </div>
                            <!-- Product Pricing -->
                            <!-- NEW FEATURE -->
                            <div class="row mb-3">
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Buying Price</label>
                                    <input name="product_buying_price" id="product_buying_price" class="form-control" type="text" autocomplete="off" placeholder="Product Buying Price" value="0" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label read-only">Price Code</label>
                                    <input name="product_price_code" id="product_price_code" class="form-control" type="text" placeholder="Price Code" readonly>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Sell Price</label>
                                    <input name="product_selling_price" class="form-control" type="text" autocomplete="off" placeholder="Product Selling Price" value="0" required>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Offer Price</label>
                                    <input name="product_offer_price" class="form-control" type="text" autocomplete="off" placeholder="Product Offer Price" value="0">
                                </div>

                            </div>
                            <!-- Product Pricing row end -->

                            <!-- end row -->
                            <!-- <div class="row mb-3">
                                <label for="example-text-input" class="col-sm-2 col-form-label">Market Com</label>
                                <div class="form-group col-sm-10">
                                    <input name="market_com" class="form-control" type="text" placeholder="Market Commission">
                                </div>
                            </div> -->
                            <!-- end row -->
                            <!-- <div class="row mb-3">
                                <label for="example-text-input" class="col-sm-2 col-form-label">Special Com</label>
                                <div class="form-group col-sm-10">
                                    <input name="special_com" class="form-control" type="text" placeholder="Special Commission">
                                </div>
                            </div> -->
                            <!-- end row -->
                            <div class="row mb-3">
                                <div class="form-group col-sm-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control summernote" placeholder="Write Description Here..." autocomplete="off" rows="4"></textarea>
                                </div>
                            </div>
                            <!-- end row -->
                            <!-- Product Image -->
                            <div class="row mb-3">
                                <div class="form-group col-sm-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Image</label>
                                    <input name="product_image" class="form-control" type="file" id="image">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <img id="showImage" class="rounded avatar-lg" src="{{ url('upload/no_image.jpg') }}" alt="Card image cap">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="form-group col-md-12 text-start" >
                                    <button type="submit" class="btn btn-success"><i class="fas fa-plus-circle"></i> Add </button>
                                </div>
                            </div>
                            <!-- end row Product Image -->

                        </div>
                    </div>
                </div> <!-- 1st end col -->
            </form>
        </div>

    </div>
</div>
<!-- JS Start -->
<script>
    tinymce.init({
        selector: '#description',
        plugins: 'lists advlist bold italic link',
        toolbar: 'bold italic | bullist numlist | link',
        menubar: false
    });
</script>
<script>
    $(document).ready(function() {
        $("#brand_id").select2();
        $("#category_id").select2();
        $("#unit_id").select2();
        $("#tax_type").select2();
    });
</script>
<!-- Tax btn Toggle START-->
<!-- <script type="text/javascript">
    $(function() {
        $(document).on('change', '#tax_type', function() {
            var tax_type = $(this).val();
            if (tax_type == 'Excluded') {
                $('.div-tax').show();
                $('#tax_type_btn').removeClass('col-sm-4');
                $('#tax_type_btn').addClass('col-sm-2');
            } else {
                $('.div-tax').hide();
                $('#tax_type_btn').removeClass('col-sm-2');
                $('#tax_type_btn').addClass('col-sm-4');
            }
        })
    })
</script> -->
<!-- Tax btn Toggle END-->
<!-- Loading Category as from Brand -->
<script type="text/javascript">
    $(function() {
        $(document).on('change', '#brand_id', function() {
            var brand_id = $(this).val();
            if (brand_id == -1) {
                // alert(brand_id);
                $('.new_brand').show();
                $('#brand_col').removeClass('col-sm-3');
                $('#brand_col').addClass('col-sm-1');

                $.ajax({
                    url: "{{ route('get-category-by-brand') }}",
                    type: "GET",
                    data: {
                        brand_id: brand_id
                    },
                    success: function(data) {
                        product_list = data;
                        var html = '<option value="0">Select Category</option>';
                        $.each(data, function(key, v) {
                            html += '<option value="' + v.id + '"> ' + v.name + '</option>';
                        });
                        html += '<option value="-1">+ Add</option>';
                        $('#category_id').html(html);
                    }
                })
            } else {
                $('.new_brand').hide();
                $('#brand_col').addClass('col-sm-3');
                $('.new_category').hide();
                $('#category_col').addClass('col-sm-3');
                $.ajax({
                    url: "{{ route('get-category-by-brand') }}",
                    type: "GET",
                    data: {
                        brand_id: brand_id
                    },
                    success: function(data) {
                        product_list = data;
                        var html = '<option value="0">Select Category</option>';
                        $.each(data, function(key, v) {
                            html += '<option value="' + v.id + '"> ' + v.name + '</option>';
                        });
                        html += '<option value="-1">+ Add</option>';
                        $('#category_id').html(html);
                    }
                })
            }


        });
    });
</script>
<!-- Unit Option -->
<script type="text/javascript">
    $(function() {
        $(document).on('change', '#unit_id', function() {
            var unit_id = $(this).val();

            if (unit_id == -1) {
                $('#new_unit').show();
                $('#unit_col').removeClass('col-sm-2');
                $('#unit_col').addClass('col-sm-1');

            } else {
                $('#new_unit').hide();
                $('#unit_col').addClass('col-sm-2');
            }
        });
    });
</script>
<!-- Category Option -->
<script type="text/javascript">
    $(function() {
        $(document).on('change', '#category_id', function() {
            var category_id = $(this).val();

            if (category_id == -1) {
                $('.new_category').show();
                $('#category_col').removeClass('col-sm-3');
                $('#category_col').addClass('col-sm-1');

            } else {
                $('.new_category').hide();
                $('#category_col').addClass('col-sm-3');
            }
        });
    });
</script>
<!-- Generate SKU START-->
<script type="text/javascript">
    $(document).ready(function() {
        function getRndInteger(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        function generateSKU() {
            // let brand = $("#brand_id").siblings().text();
            // let category = $("#category_id").val();
            let randomNum = getRndInteger(100, 999);
            //     $("#sku").val((brand != 0 && category != 0 && brand != -1 && category != -1) ? `${brand}-${category}-${randomNum}` : randomNum);
            $("#sku").val(randomNum);
        }
        $("#productName").on("blur", generateSKU);
        // $("#brand_id, #category_id").on("change", generateSKU);
    });
</script>
<!-- Generate SKU END-->
<!-- Product price code generation -->
<script type="text/javascript">
    $(document).ready(function() {
        const numberDictionary = @json($productPriceCode -> pluck('code', 'number'));

        function numberToCode(buyingPrice) {
            let num = buyingPrice.toString();
            let result = "";
            let i = 0;

            while (i < num.length) {
                if (i + 2 < num.length && num.substring(i, i + 3) === "000" && numberDictionary?.["000"]) {
                    result += numberDictionary["000"];
                    i += 3;
                } else if (i + 1 < num.length && num.substring(i, i + 2) === "00" && numberDictionary?.["00"]) {
                    result += numberDictionary["00"];
                    i += 2;
                } else {
                    result += numberDictionary?.[num[i]] || "";
                    i++;
                }
            }
            return result.toUpperCase();
        }

        $("#product_buying_price").on("blur", function() {
            let buyingPrice = $(this).val().trim();
            let html = buyingPrice ? numberToCode(parseInt(buyingPrice)) : "";
            $("#product_price_code").val(html);
        });
    });
</script>
<!-- Generate Product Code -->
<script>
    $(document).ready(function() {
        function getRndInteger(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        $("#productName").on("blur", function() {
            $("#product_code").val(getRndInteger(999, 99999));
        });

        $("#productCode").on("click", function() {
            $("#product_code").val(getRndInteger(999, 99999));
        });
    });
</script>
<!-- Java Script validation for empty form -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#myForm').validate({
            rules: {
                name: {
                    required: true,
                },
                product_sort_name: {
                    required: true,
                },
                brand_id: {
                    required: true,
                },
                unit_id: {
                    required: true,
                },
                quantity: {
                    required: true,
                },
                product_buying_price: {
                    required: true,
                },
                product_selling_price: {
                    required: true,
                },
                sku: {
                    required: false,
                },
                product_image: {
                    required: false,
                },
            },

            messages: {
                name: {
                    required: 'Please Enter the Product Name.',
                },
                product_sort_name: {
                    required: 'Please Enter the Product Sort Name.',
                },
                product_buying_price: {
                    required: 'Please Enter the Product Buying Price.',
                },
                product_selling_price: {
                    required: 'Please Enter the Product Selling Price',
                },
                quantity: {
                    required: 'please Enter Stocks.',
                },
                category_id: {
                    required: 'Please select the category name',
                },
                brand_id: {
                    required: 'Please select the brand name',
                },
                unit_id: {
                    required: 'Please select the unit name',
                },
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
        });
    });
</script>
<!-- Product Image show -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>


@endsection