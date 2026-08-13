<?php

namespace App\Http\Controllers;

use App\Models\ElectronicInvoice;
use App\Services\Invoicing\ElectronicInvoicePdf;
use Symfony\Component\HttpFoundation\Response;

class ElectronicInvoiceController extends Controller
{
    public function pdf(ElectronicInvoice $invoice, ElectronicInvoicePdf $pdf): Response
    {
        return $pdf->download($invoice);
    }

    public function preview(ElectronicInvoice $invoice, ElectronicInvoicePdf $pdf): Response
    {
        return $pdf->stream($invoice);
    }
}
