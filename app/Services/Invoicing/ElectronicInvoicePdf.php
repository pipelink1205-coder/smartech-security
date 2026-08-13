<?php

namespace App\Services\Invoicing;

use App\Models\ElectronicInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ElectronicInvoicePdf
{
    public function make(ElectronicInvoice $invoice)
    {
        $invoice->loadMissing(['details', 'quote']);

        return Pdf::loadView('pdf.electronic-invoice', ['invoice' => $invoice])
            ->setPaper('a4', 'portrait');
    }

    public function download(ElectronicInvoice $invoice): Response
    {
        $name = ($invoice->full_number ?: 'FACTURA-'.$invoice->id).'.pdf';

        return $this->make($invoice)->download($name);
    }

    public function stream(ElectronicInvoice $invoice): Response
    {
        return $this->make($invoice)->stream(($invoice->full_number ?: 'FACTURA-'.$invoice->id).'.pdf');
    }

    public function store(ElectronicInvoice $invoice): string
    {
        $binary = $this->make($invoice)->output();
        $path = 'dian/pdf/'.$invoice->id.'-'.($invoice->full_number ?: 'draft').'.pdf';
        Storage::disk('local')->put($path, $binary);
        $invoice->update(['pdf_path' => $path]);

        return $path;
    }
}
