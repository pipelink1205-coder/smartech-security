<?php

namespace App\Filament\Resources\WhatsappLeads;

use App\Filament\Resources\WhatsappLeads\Pages\EditWhatsappLead;
use App\Filament\Resources\WhatsappLeads\Pages\ListWhatsappLeads;
use App\Filament\Resources\WhatsappLeads\Schemas\WhatsappLeadForm;
use App\Filament\Resources\WhatsappLeads\Tables\WhatsappLeadsTable;
use App\Models\WhatsappLead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WhatsappLeadResource extends Resource
{
    protected static ?string $model = WhatsappLead::class;

    protected static ?string $navigationLabel = 'WhatsApp';

    protected static ?string $slug = 'whatsapp-leads';

    protected static string|\UnitEnum|null $navigationGroup = 'Operaciones';

    protected static ?string $modelLabel = 'contacto WhatsApp';

    protected static ?string $pluralModelLabel = 'contactos WhatsApp';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    public static function form(Schema $schema): Schema
    {
        return WhatsappLeadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WhatsappLeadsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWhatsappLeads::route('/'),
            'edit' => EditWhatsappLead::route('/{record}/edit'),
        ];
    }
}
