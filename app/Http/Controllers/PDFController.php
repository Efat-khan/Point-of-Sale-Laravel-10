<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function dashboardReportPDF($startDate,$endDate,$filterName='Today',$total_amount,$total_profit,$total_paid,$total_due){
        // dd($filterName);
        set_time_limit(120);
        $data = [
            // 'allData' => $allData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filterName' => $filterName,
            'total_amount' => $total_amount,
            'total_profit' => $total_profit,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
        ];
        // dd($data);
        $pdf = Pdf::loadView('backend.pdf.dashboardReportPrint',$data);
        return $pdf->stream();
    }
}
