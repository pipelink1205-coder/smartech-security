<?php

namespace App\Filament\Resources\ElectronicInvoices;

use App\Filament\Resources\ElectronicInvoices\Pages\EditElectronicInvoice;
use App\Filament\Resources\ElectronicInvoices\Pages\ListElectronicInvoices;
use App\Filament\Resources\ElectronicInvoices\Schemas\ElectronicInvoiceForm;
use App\Filament\Resources\ElectronicInvoices\Tables\ElectronicInvoicesTable;
use App\Models\ElectronicInvoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ElectronicInvoiceResource extends Resource
{
    protected static ?string $model = ElectronicInvoice::class;

    protected static ?string $navigationLabel = 'Facturas';

    protected static string|\UnitEnum|null $navigationGroup = 'Operaciones';

    protected static ?string $modelLabel = 'factura';

    protected static ?string $pluralModelLabel = 'facturas';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    public static function form(Schema $schema): Schema
    {
        return ElectronicInvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ElectronicInvoicesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['details', 'quote']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListElectronicInvoices::route('/'),
            'edit' => EditElectronicInvoice::route('/{record}/edit'),
        ];
    }
}
