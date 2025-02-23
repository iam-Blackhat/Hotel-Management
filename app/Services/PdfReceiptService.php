<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfReceiptService
{
    public function generateReceipt($order)
    {
        // Set custom paper size (58mm or 80mm width for thermal printer)
        $pdf = Pdf::loadView('pdf.receipt', compact('order'))
                  ->setPaper('A7', 'portrait'); // Width x Height (in points)

        return $pdf->output();
    }
}
