<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\Pages\Concerns\SyncsQuoteClient;
use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuote extends CreateRecord
{
    use SyncsQuoteClient;

    protected static string $resource = QuoteResource::class;
}
