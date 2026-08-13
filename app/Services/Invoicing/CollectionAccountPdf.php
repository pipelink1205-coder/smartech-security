<?php

namespace App\Services\Invoicing;

use App\Models\CollectionAccount;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CollectionAccountPdf
{
    public function make(CollectionAccount $account)
    {
        $account->loadMissing(['items', 'quote']);

        return Pdf::loadView('pdf.collection-account', ['account' => $account])
            ->setPaper('a4', 'portrait');
    }

    public function download(CollectionAccount $account): Response
    {
        return $this->make($account)->download($account->number.'.pdf');
    }

    public function stream(CollectionAccount $account): Response
    {
        return $this->make($account)->stream($account->number.'.pdf');
    }

    public function store(CollectionAccount $account): string
    {
        $path = 'collection-accounts/pdf/'.$account->number.'.pdf';
        Storage::disk('local')->put($path, $this->make($account)->output());
        $account->update(['pdf_path' => $path]);

        return $path;
    }
}
