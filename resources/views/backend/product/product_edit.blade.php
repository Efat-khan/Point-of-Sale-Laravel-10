@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Product <a href="{{ route('product.add') }}" class="btn btn-dark btn-rounded waves-effect waves-light"><i class="fas fa-plus-circle"> </i> ADD </a></h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                            <li class="m-2 breadcrumb-item"><a href="{{route('product.all')}}">BACK</a></li>
                            <!-- <li class=""><a href="{{route('product.all')}}" class="btn btn-dark btn-rounded waves-effect waves-light">
                                <i class="fa fa-chevron-circle-left"> Back </i></a></li> -->
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <form method="POST" action="{{ route('product.update')}}" id="myForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $product->id }}">

                            <div class="row mb-3">
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Product Name</label>
                                    <input name="name" id="productName" class="form-control" type="text" value="{{$product->name}}">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Product Sort Name</label>
                                    <input name="product_sort_name" class="form-control" type="text" value="{{$product->product_sort_name}}">
                                </div>
                                <!-- Brand Dropdown button  -->
                                <div class="form-group col-sm-3" id="brand_col">
                                    <label class="col-sm-6 col-form-label">Brand </label>
                                    <select class="form-select" name="brand_id" id="brand_id" aria-label="Default select example">
                                        <option value="0" selected>Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{$brand->id}}" {{$brand->id == $product->brand_id ? 'selected' : ''}}>{{$brand->name}}</option>
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
                                    <select class="form-select " name="category_id" id="category_id" aria-label="Default select example">
                                        <option value="0">Select Category</option>

                                        @foreach($category as $cat)
                                        <option value="{{$cat->id}}" {{$cat->id == $product->category_id ? 'selected' : ''}}>{{$cat->name}}</option>
                                        @endforeach
                                        <option value="-1">+ Add</option>
                                    </select>
                                </div>

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
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group col-sm-12">
                                        <label for="part_number" class="col-sm-12 col-form-label">Part Number</label>
                                        <input name="part_number" id="part_number" class="form-control" type="text" value="{{$product->part_number}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group col-sm-12">
                                        <label for="model_number" class="col-sm-12 col-form-label">Model Number</label>
                                        <input name="model_number" id="model_number" class="form-control" type="text" value="{{$product->model_number}}">
                                    </div>
                                </div>
                            </div>


                            <!-- Units Dropdown button  -->
                            <div class="row mb-3">
                                <div class="col-sm-2" id="unit_col">
                                    <label class="col-sm-4 col-form-label">Unit </label>
                                    <select class="form-group form-select" name="unit_id" id="unit_id" aria-label="Default select example">
                                        @foreach($unit as $uni)
                                        <option value="{{$uni->id}}" {{$uni->id == $product->unit_id ? 'selected' : ''}}>{{$uni->name}}</option>
                                        @endforeach
                                        <option value="-1">+ Add</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-1" id="new_unit" style="display: none;">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">+Add Unit</label>
                                    <input name="unit_name" id="unit_name" class="form-control" type="text" autocomplete="off" placeholder="New Unit" required>
                                </div>
                                <!-- Units button End Row -->
                                <div class="form-group col-sm-4" id="tax_type_btn">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">VAT Type</label>
                                    <select name="product_taxes[]" multiple="multiple" id="tax_type" class="form-group form-select">
                                        <option value="TaxFree">VAT Free</option>
                                        @foreach ($selected_tax as $key=>$value)
                                        <option value="{{$value->id}}" selected>{{$value->name}}</option>
                                        @endforeach
                                        @foreach ($unselected_tax as $key=>$value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- <div class="form-group col-sm-2 div-tax" style="display: {{$product->tax_type == 'Included' ?'none':'block'}};">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Tax(%) [e.g : 5%]</label>
                                    <input name="tax" id="tax" class="form-control" type="number" autocomplete="off" value="{{$product->tax*100}}">
                                </div> -->
                                <div class="form-group col-sm-2">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">SKU</label>
                                    <input name="sku" class="form-control" type="text" value="{{$product->sku}}" readonly>
                                </div>
                                <!-- Product Code -->
                                <div class="form-group col-sm-2">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Product Code</label>
                                    <label id="productCode" class="btn btn-info btn-sm btn-rounded waves-effect waves-light">Generate</label>
                                    <input name="product_code" id="product_code" class="form-control" type="text" value="{{$product->product_code}}" readonly>
                                </div>
                                <!-- Quantity -->
                                <div class="form-group col-sm-2">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Product Stock</label>
                                    <input name="quantity" class="form-control" type="number" value="{{$product->quantity}}">
                                </div>

                            </div>

                            <!-- Product Pricing -->
                            <div class="row mb-3">
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Product Buying Price</label>
                                    <input name="product_buying_price" id="product_buying_price" class="form-control" type="text" value="{{$product->product_buying_price}}">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label read-only">Product Price Code</label>
                                    <input name="product_price_code" id="product_price_code" class="form-control" type="text" value="{{$product->product_price_code}}" readonly>
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Product Sell Price</label>
                                    <input name="product_selling_price" class="form-control" type="text" value="{{$product->product_price}}">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">Product Offer Price</label>
                                    <input name="product_offer_price" class="form-control" type="text" value="{{$product->product_offer_price}}">
                                </div>

                            </div>
                            <!-- Product Pricing row end -->
                            <!-- end row -->

                            <div class="row mb-3">
                                <!-- <div class="form-group col-sm-4">
                                    <label for="example-text-input" class="col-sm-6 col-form-label">DB. Com (%)</label>
                                    <input name="db_com" class="form-control" type="text" placeholder="DB Commission (%)" value="{{$product->db_com}}">
                                </div> -->

                            </div>
                            <!-- end row -->

                            <div class="row mb-3">
                                <div class="form-group col-sm-12">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control summernote" value="" rows="3">{!! $product->description  !!}</textarea>
                                </div>
                            </div>
                            <!-- end row -->

                            <!-- Product Image -->
                            <div class="row mb-3">
                                <div class="form-group col-sm-10">
                                    <label for="example-text-input" class="col-sm-2 col-form-label">Product Image</label>
                                    <input name="product_image" class="form-control" type="file" id="image">
                                </div>
                            </div>
                            <!-- end row -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <img id="showImage" class="rounded avatar-lg" src="{{asset($product->product_image) }}" alt="Card image cap">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="form-group col-md-12 text-start">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Update </button>
                                </div>
                            </div>


                    </div>
                </div>
            </div> <!-- end col -->

            </form>
        </div>

    </div>
</div>
<script>
    $(document).ready(function() {
        $("#brand_id").select2();
        $("#category_id").select2();
        $("#unit_id").select2();
        $("#tax_type").select2();
    });
</script>
<!-- All JS -->
<script>
    function load_category() {
        // console.log("Page is fully loaded");
        var brand_id = $('#brand_id').val();
        var category_id = '{{ $product->category_id }}';
        // alert(category_id);
        $.ajax({
            url: "{{ route('get-category-by-brand') }}",
            type: "GET",
            data: {
                brand_id: brand_id
            },
            success: function(data) {
                product_list = data;
                var html = '<option value="">Select Category</option>';
                $.each(data, function(key, v) {
                    var selected = (v.id == category_id) ? ' selected' : '';
                    html += '<option value=" ' + v.id + '" ' + selected + ' > ' + v.name + '</option>';
                });
                $('#category_id').html(html);
            }
        })
    }

    window.onload = load_category;
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
    $('#productCode').on("click", function() {
        // alert(getRndInteger(9999999999,99999999999));
        document.getElementById('product_code').value = getRndInteger(999, 99999);

        function getRndInteger(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

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
                category_id: {
                    required: false,
                },
                brand_id: {
                    required: false,
                },
                unit_id: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: 'Please Enter the Product name',
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
<script type="text/javascript">
    $("#submit").on('click', function() {
        $('#category_id').attr('disabled', false)
    });
</script>


<!-- Customer Image show -->
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