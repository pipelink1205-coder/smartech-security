<?php

namespace App\Filament\Resources\WhatsappLeads\Pages;

use App\Filament\Resources\WhatsappLeads\WhatsappLeadResource;
use Filament\Resources\Pages\ListRecords;

class ListWhatsappLeads extends ListRecords
{
    protected static string $resource = WhatsappLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
