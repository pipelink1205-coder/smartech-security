<?php

namespace App\Filament\Resources\CommercialConcepts\Schemas;

use App\Models\QuoteCatalogItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommercialConceptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Concepto comercial')
                ->description('Plantilla reutilizable para cotizaciones. No representa inventario ni existencias.')
                ->schema([
                    Select::make('type')
                        ->label('Tipo')
                        ->options(QuoteCatalogItem::TYPES)
                        ->default('product')
                        ->required(),
                    TextInput::make('name')
                        ->label('Concepto')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label('Código interno')
                        ->maxLength(80)
                        ->helperText('Opcional; nunca se muestra al cliente.'),
                    TextInput::make('category')
                        ->label('Categoría')
                        ->maxLength(80),
                    Textarea::make('description')
                        ->label('Descripción sugerida')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                    Select::make('unit')
                        ->label('Unidad')
                        ->options(QuoteCatalogItem::UNITS)
                        ->default('unidad')
                        ->required(),
                    TextInput::make('default_unit_price')
                        ->label('Valor unitario sugerido')
                        ->numeric()
                        ->prefix('$')
                        ->default(0)
                        ->minValue(0)
                        ->required(),
                    TextInput::make('default_tax_rate')
                        ->label('IVA sugerido')
                        ->numeric()
                        ->suffix('%')
                        ->default(19)
                        ->minValue(0)
                        ->maxValue(100)
                        ->required(),
                    Toggle::make('is_active')
                        ->label('Disponible para cotizar')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
