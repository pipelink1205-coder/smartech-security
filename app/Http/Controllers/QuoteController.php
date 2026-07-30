<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Mail\QuoteGenerated;
use App\Mail\NewLeadAlert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:100',
            'service' => 'required|string',
            'zone'    => 'nullable|string',
            'message' => 'nullable|string|max:1000',
        ]);

        $quote = Quote::create([
            ...$data,
            'status' => 'new',
        ]);

        if ($quote->email) {
            Mail::to($quote->email)->send(new QuoteGenerated($quote));
        }
        Mail::to(config('contact.admin_email'))->send(new NewLeadAlert($quote));

        return back()->with('success', '¡Solicitud recibida! Le contactaremos pronto.');
    }

    public function pdf(Quote $quote)
    {
        $quote->load('items');

        $pdf = Pdf::loadView('pdf.quote', compact('quote'))
            ->setPaper('a4', 'portrait');

        $filename = ($quote->quote_number ?: 'COT-'.$quote->id).'.pdf';

        return $pdf->download($filename);
    }

    public function preview(Quote $quote)
    {
        $quote->load('items');

        $filename = ($quote->quote_number ?: 'COT-'.$quote->id).'.pdf';

        return Pdf::loadView('pdf.quote', compact('quote'))
            ->setPaper('a4', 'portrait')
            ->stream($filename);
    }
}
