<?php

namespace App\Filament\Resources\CollectionAccounts;

use App\Filament\Resources\CollectionAccounts\Pages\EditCollectionAccount;
use App\Filament\Resources\CollectionAccounts\Pages\ListCollectionAccounts;
use App\Filament\Resources\CollectionAccounts\Schemas\CollectionAccountForm;
use App\Filament\Resources\CollectionAccounts\Tables\CollectionAccountsTable;
use App\Models\CollectionAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CollectionAccountResource extends Resource
{
    protected static ?string $model = CollectionAccount::class;

    protected static ?string $navigationLabel = 'Cuentas de cobro';

    protected static string|\UnitEnum|null $navigationGroup = 'Operaciones';

    protected static ?string $modelLabel = 'cuenta de cobro';

    protected static ?string $pluralModelLabel = 'cuentas de cobro';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function form(Schema $schema): Schema
    {
        return CollectionAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CollectionAccountsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['items', 'quote']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCollectionAccounts::route('/'),
            'edit' => EditCollectionAccount::route('/{record}/edit'),
        ];
    }
}
