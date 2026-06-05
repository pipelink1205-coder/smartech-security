<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp')
                ->label('Abrir WhatsApp')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->url(fn (Quote $record): string => $record->whatsapp_link)
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
