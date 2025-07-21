<?php
use NumberToWords\NumberToWords;

if (!function_exists('convertNumberToWords')) {
    function convertNumberToWords($number)
    {
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');
        return $numberTransformer->toWords($number);
    }
}

//In Bangladesh, the commonly used number format for money is based on the Indian Numbering System, which uses:

function convertToBangladeshiWords($number)
{
    $words = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen', 'Twenty', 30 => 'Thirty', 40 => 'Forty',
        50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
    ];

    $places = [
        'Crore'    => 10000000,
        'Lakh'     => 100000,
        'Thousand' => 1000,
        'Hundred'  => 100,
    ];

    // Step 1: Clean number
    if (!is_numeric($number)) {
        return 'Invalid number';
    }

    $num = (int)floor($number); // remove anything after the decimal
    if ($num === 0) return 'Zero Taka Only';

    $result = '';

    // Step 2: Process each digit group (Crore, Lakh, etc.)
    foreach ($places as $label => $value) {
        if ($num >= $value) {
            $count = floor($num / $value);
            $num = $num % $value;

            if ($count > 0) {
                $result .= convertDoubleDigit($count, $words) . ' ' . $label . ' ';
            }
        }
    }

    // Step 3: Handle the last part (below hundred)
    if ($num > 0) {
        $result .= convertDoubleDigit($num, $words) . ' ';
    }

    return strtoupper(trim($result)) . ' TAKA ONLY';
}

function convertDoubleDigit($number, $words)
{
    if ($number <= 20) {
        return $words[$number];
    } else {
        $tens = floor($number / 10) * 10;
        $units = $number % 10;
        return trim($words[$tens] . ' ' . $words[$units]);
    }
}

?>