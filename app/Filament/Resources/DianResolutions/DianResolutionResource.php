<?php

namespace App\Filament\Resources\DianResolutions;

use App\Filament\Resources\DianResolutions\Pages\CreateDianResolution;
use App\Filament\Resources\DianResolutions\Pages\EditDianResolution;
use App\Filament\Resources\DianResolutions\Pages\ListDianResolutions;
use App\Filament\Resources\DianResolutions\Schemas\DianResolutionForm;
use App\Filament\Resources\DianResolutions\Tables\DianResolutionsTable;
use App\Models\DianResolution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DianResolutionResource extends Resource
{
    protected static ?string $model = DianResolution::class;

    protected static ?string $navigationLabel = 'Resoluciones DIAN';

    protected static string|\UnitEnum|null $navigationGroup = 'Administración';

    protected static ?string $modelLabel = 'resolución DIAN';

    protected static ?string $pluralModelLabel = 'resoluciones DIAN';

    protected static ?string $recordTitleAttribute = 'numero_resolucion';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHashtag;

    public static function form(Schema $schema): Schema
    {
        return DianResolutionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DianResolutionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDianResolutions::route('/'),
            'create' => CreateDianResolution::route('/create'),
            'edit' => EditDianResolution::route('/{record}/edit'),
        ];
    }
}
