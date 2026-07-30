<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Models\Quote;
use App\Models\QuoteCatalogItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Cliente')
                    ->description('Datos del lead o del cliente de la cotización formal.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label('Correo')
                            ->email()
                            ->maxLength(100),
                        TextInput::make('company')
                            ->label('Empresa')
                            ->maxLength(120),
                        TextInput::make('service')
                            ->label('Servicio')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('project_title')
                            ->label('Proyecto / obra')
                            ->placeholder('Ej.: Colegio Cola de El Zorro — cableado')
                            ->maxLength(180),
                        TextInput::make('zone')
                            ->label('Zona')
                            ->maxLength(80),
                        TextInput::make('client_address')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->columnSpan(2),
                        Textarea::make('message')
                            ->label('Mensaje / necesidad del cliente')
                            ->rows(2)
                            ->columnSpanFull(),
                        Select::make('intent')
                            ->label('Intención del lead')
                            ->options(Quote::INTENTS)
                            ->required()
                            ->default('info'),
                        DatePicker::make('preferred_visit_date')
                            ->label('Fecha preferida visita')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText('Preferencia del cliente; confirmar disponibilidad.'),
                        Select::make('preferred_visit_slot')
                            ->label('Franja preferida')
                            ->options(Quote::VISIT_SLOTS)
                            ->placeholder('—'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->columnSpanFull()
                    ->collapsible(),

                Section::make('Ítems de cotización')
                    ->description('Seleccione un concepto frecuente o escriba uno manualmente. Los códigos internos nunca se muestran al cliente.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->label('Líneas')
                            ->orderColumn('sort_order')
                            ->reorderable()
                            ->defaultItems(0)
                            ->schema([
                                Select::make('quote_catalog_item_id')
                                    ->label('Concepto frecuente')
                                    ->options(fn () => QuoteCatalogItem::active()
                                        ->orderBy('name')
                                        ->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Escribir manualmente')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set): void {
                                        if (blank($state)) {
                                            return;
                                        }

                                        $catalog = QuoteCatalogItem::active()->find($state);

                                        if (! $catalog) {
                                            return;
                                        }

                                        $set('code', $catalog->code);
                                        $set('type', $catalog->type);
                                        $set('concept', $catalog->name);
                                        $set('description', $catalog->description);
                                        $set('unit', $catalog->unit);
                                        $set('unit_price', (float) $catalog->default_unit_price);
                                        $set('tax_rate', (float) $catalog->default_tax_rate);
                                    })
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 4,
                                    ]),
                                Select::make('type')
                                    ->label('Tipo')
                                    ->options(QuoteCatalogItem::TYPES)
                                    ->required()
                                    ->default('product')
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 3,
                                    ]),
                                TextInput::make('concept')
                                    ->label('Concepto')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 5,
                                    ]),
                                TextInput::make('code')
                                    ->label('Código interno')
                                    ->maxLength(80)
                                    ->helperText('Opcional. No aparece en el PDF.')
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 3,
                                    ]),
                                TextInput::make('description')
                                    ->label('Descripción')
                                    ->required()
                                    ->maxLength(1000)
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 9,
                                    ]),
                                TextInput::make('quantity')
                                    ->label('Cant.')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(0.01)
                                    ->live()
                                    ->columnSpan([
                                        'default' => 6,
                                        'lg' => 2,
                                    ]),
                                Select::make('unit')
                                    ->label('Unidad')
                                    ->options(QuoteCatalogItem::UNITS)
                                    ->default('unidad')
                                    ->required()
                                    ->columnSpan([
                                        'default' => 6,
                                        'lg' => 2,
                                    ]),
                                TextInput::make('unit_price')
                                    ->label('Vr. unitario')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->minValue(0)
                                    ->live()
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 2,
                                    ]),
                                TextInput::make('discount_percent')
                                    ->label('Descuento')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->dehydrateStateUsing(fn ($state): float => filled($state) ? (float) $state : 0)
                                    ->live()
                                    ->columnSpan([
                                        'default' => 6,
                                        'lg' => 2,
                                    ]),
                                TextInput::make('tax_rate')
                                    ->label('IVA')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(19)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->dehydrateStateUsing(fn ($state): float => filled($state) ? (float) $state : 0)
                                    ->live()
                                    ->columnSpan([
                                        'default' => 6,
                                        'lg' => 2,
                                    ]),
                                Placeholder::make('calculated_total')
                                    ->label('Total línea')
                                    ->content(function (Get $get): string {
                                        $gross = (float) ($get('quantity') ?: 0) * (float) ($get('unit_price') ?: 0);
                                        $net = $gross * (1 - ((float) ($get('discount_percent') ?: 0) / 100));
                                        $total = $net * (1 + ((float) ($get('tax_rate') ?: 0) / 100));

                                        return '$'.number_format($total, 0, ',', '.').' COP';
                                    })
                                    ->columnSpan([
                                        'default' => 12,
                                        'lg' => 2,
                                    ]),
                            ])
                            ->columns(12)
                            ->columnSpanFull()
                            ->addActionLabel('Agregar ítem')
                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null),
                        DatePicker::make('valid_until')
                            ->label('Válida hasta')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Textarea::make('terms')
                            ->label('Condiciones comerciales')
                            ->rows(4)
                            ->default(fn () => config('quotes.default_terms'))
                            ->dehydrateStateUsing(fn ($state): ?string => blank($state) || trim((string) $state) === '0'
                                ? null
                                : trim((string) $state))
                            ->columnSpanFull(),
                        Textarea::make('payment_terms')
                            ->label('Forma de pago')
                            ->rows(3)
                            ->default(fn () => config('quotes.default_payment_terms'))
                            ->dehydrateStateUsing(fn ($state): ?string => blank($state) || trim((string) $state) === '0'
                                ? null
                                : trim((string) $state))
                            ->columnSpanFull(),
                        Textarea::make('warranty_terms')
                            ->label('Garantía')
                            ->rows(3)
                            ->default(fn () => config('quotes.default_warranty_terms'))
                            ->dehydrateStateUsing(fn ($state): ?string => blank($state) || trim((string) $state) === '0'
                                ? null
                                : trim((string) $state))
                            ->columnSpanFull(),
                        TextInput::make('advisor_name')
                            ->label('Asesor comercial')
                            ->maxLength(120),
                        TextInput::make('advisor_title')
                            ->label('Cargo del asesor')
                            ->default('Asesor comercial')
                            ->maxLength(120),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpanFull(),

                Section::make('Seguimiento')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options(Quote::STATUSES)
                            ->required()
                            ->default('new'),
                        TextInput::make('quote_number')
                            ->label('Número')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Se genera al guardar'),
                        TextInput::make('price_min')
                            ->label('Rango mín. (legacy)')
                            ->numeric()
                            ->prefix('$')
                            ->helperText('Opcional. Ya no se usa en el formulario público.'),
                        TextInput::make('price_max')
                            ->label('Rango máx. (legacy)')
                            ->numeric()
                            ->prefix('$'),
                        Textarea::make('notes')
                            ->label('Notas internas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->collapsed(),
            ]);
    }
}
