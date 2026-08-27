<?php

namespace App\Filament\Resources\WhatsappLeads;

use App\Filament\Resources\WhatsappLeads\Pages\ListWhatsappLeads;
use App\Filament\Resources\WhatsappLeads\Tables\WhatsappLeadsTable;
use App\Models\WhatsappLead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WhatsappLeadResource extends Resource
{
    protected static ?string $model = WhatsappLead::class;

    protected static ?string $navigationLabel = 'Clics WhatsApp';

    protected static ?string $slug = 'whatsapp-leads';

    protected static string|\UnitEnum|null $navigationGroup = 'Operaciones';

    protected static ?string $modelLabel = 'clic de WhatsApp';

    protected static ?string $pluralModelLabel = 'clics de WhatsApp';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return WhatsappLeadsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWhatsappLeads::route('/'),
        ];
    }
}
