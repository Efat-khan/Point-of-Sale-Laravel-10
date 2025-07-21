<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>POS Invoice Print</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        p.inline {
            display: inline-block;
        }

        span {
            font-size: 13px;
        }
    </style>
    <style type="text/css" media="print">
        @page {
            size: auto;
            /* auto is the initial value */
            margin: 0mm;
            /* this affects the margin in the printer settings */

        }
    </style>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <script>
        function printDiv() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;

        }
        window.onload = printDiv;

        function performAction() {
            window.location.href = '{{ route("productLabelsprint.index")}}';
        }
        // Execute performAction after 30 seconds (1000 milliseconds) 1second
        setTimeout(performAction, 100);
    </script>
    @php
    $org = App\Models\OrgDetails::first();
    @endphp

    <div class="row mx-auto" id="printableArea">
        <div class="col-md-12">
            <div style="margin-left: 5%">
                @for ($i=1; $i<=$qty; $i++)
                    <p class='inline pb-5'>
                    <span>
                        <b>{{$org->org_name_en}}</b><br>
                        <b>{{$product->name}}&nbsp&nbsp({{ $product->product_code }})</b></span>
                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($product->product_code, 'C128') }}" alt="barcode" />
                    <span><b>{{$product->product_price_code}}&nbsp&nbsp|&nbsp&nbspPrice:{{$product->product_price}} </b><span>

                            </p>&nbsp&nbsp&nbsp&nbsp&nbsp
                            @endfor

            </div>
        </div>
    </div>
</body>

</html>