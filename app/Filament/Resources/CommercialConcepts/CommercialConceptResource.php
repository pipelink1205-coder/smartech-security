<?php

namespace App\Filament\Resources\CommercialConcepts;

use App\Filament\Resources\CommercialConcepts\Pages\CreateCommercialConcept;
use App\Filament\Resources\CommercialConcepts\Pages\EditCommercialConcept;
use App\Filament\Resources\CommercialConcepts\Pages\ListCommercialConcepts;
use App\Filament\Resources\CommercialConcepts\Schemas\CommercialConceptForm;
use App\Filament\Resources\CommercialConcepts\Tables\CommercialConceptsTable;
use App\Models\QuoteCatalogItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommercialConceptResource extends Resource
{
    protected static ?string $model = QuoteCatalogItem::class;

    protected static ?string $navigationLabel = 'Conceptos comerciales';

    protected static string|\UnitEnum|null $navigationGroup = 'Operaciones';

    protected static ?string $modelLabel = 'concepto comercial';

    protected static ?string $pluralModelLabel = 'conceptos comerciales';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CommercialConceptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommercialConceptsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommercialConcepts::route('/'),
            'create' => CreateCommercialConcept::route('/create'),
            'edit' => EditCommercialConcept::route('/{record}/edit'),
        ];
    }
}
