<?php

namespace App\Filament\Resources\HomeCovers;

use App\Filament\Resources\HomeCovers\Pages\CreateHomeCover;
use App\Filament\Resources\HomeCovers\Pages\EditHomeCover;
use App\Filament\Resources\HomeCovers\Pages\ListHomeCovers;
use App\Filament\Resources\HomeCovers\Schemas\HomeCoverForm;
use App\Filament\Resources\HomeCovers\Tables\HomeCoversTable;
use App\Models\HomeCover;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HomeCoverResource extends Resource
{
    protected static ?string $model = HomeCover::class;

    protected static ?string $slug = 'portadas';

    protected static ?string $navigationLabel = 'Portadas';

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido del sitio';

    protected static ?string $modelLabel = 'portada';

    protected static ?string $pluralModelLabel = 'portadas';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    public static function form(Schema $schema): Schema
    {
        return HomeCoverForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeCoversTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomeCovers::route('/'),
            'create' => CreateHomeCover::route('/create'),
            'edit' => EditHomeCover::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $cover = HomeCover::current();

            return $cover->exists && ! $cover->is_fallback ? $cover->kindLabel() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
