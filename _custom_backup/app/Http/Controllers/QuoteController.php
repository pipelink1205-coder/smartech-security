<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Mail\QuoteGenerated;
use App\Mail\NewLeadAlert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

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

        // Calcular precio estimado automáticamente
        $pricing = config('quotes.pricing');
        [$min, $max] = $pricing[$data['service']] ?? [0, 0];

        $quote = Quote::create([
            ...$data,
            'price_min' => $min,
            'price_max' => $max,
        ]);

        // Enviar emails
        if ($quote->email) {
            Mail::to($quote->email)->send(new QuoteGenerated($quote));
        }
        Mail::to(config('mail.admin_email'))->send(new NewLeadAlert($quote));

        return back()->with('success', '¡Cotización enviada! Le responderemos pronto.');
    }

    public function pdf(Quote $quote)
    {
        $pdf = Pdf::loadView('pdf.quote', compact('quote'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("Cotizacion-STS-{$quote->id}.pdf");
    }
}
