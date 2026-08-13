<?php

namespace App\Filament\Resources\Quotes\Pages\Concerns;

use App\Domain\Quotes\ClientSync;

trait SyncsQuoteClient
{
    protected function afterCreate(): void
    {
        $this->syncQuoteClient();
    }

    protected function syncQuoteClient(): void
    {
        $record = $this->record ?? null;

        if (! $record) {
            return;
        }

        $alreadyLinked = filled($record->client_id);
        ClientSync::ensureForQuote($record);

        if (! $alreadyLinked && filled($record->client_id)) {
            $this->record = $record->fresh();
        }
    }
}
