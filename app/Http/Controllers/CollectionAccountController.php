<?php

namespace App\Http\Controllers;

use App\Models\CollectionAccount;
use App\Services\Invoicing\CollectionAccountPdf;
use Symfony\Component\HttpFoundation\Response;

class CollectionAccountController extends Controller
{
    public function pdf(CollectionAccount $account, CollectionAccountPdf $pdf): Response
    {
        return $pdf->download($account);
    }

    public function preview(CollectionAccount $account, CollectionAccountPdf $pdf): Response
    {
        return $pdf->stream($account);
    }
}
