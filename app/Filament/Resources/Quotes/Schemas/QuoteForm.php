<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Domain\Quotes\QuoteLineCalculator;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Models\Client;
use App\Models\Quote;
use App\Models\QuoteCatalogItem;
use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make([
                    'default' => 1,
                    'xl' => 12,
                ])->schema([
                    self::clientSection(),
                    self::summarySection(),
                ]),

                self::itemsSection(),
                self::termsSection(),
                self::followUpSection(),
            ]);
    }

    public const OTHER_SERVICE = '__other__';

    /**
     * @return array<string, string>
     */
    public static function serviceOptions(): array
    {
        $options = Service::query()
            ->active()
            ->ordered()
            ->pluck('name', 'name')
            ->all();

        $options['Varios servicios'] = 'Varios servicios';
        $options[self::OTHER_SERVICE] = 'Otro servicio…';

        return $options;
    }

    public static function isCatalogService(?string $name): bool
    {
        if (blank($name) || $name === self::OTHER_SERVICE) {
            return false;
        }

        $options = self::serviceOptions();
        unset($options[self::OTHER_SERVICE]);

        return isset($options[$name]);
    }

    protected static function clientSection(): Section
    {
        return Section::make('Cliente')
            ->description('Busca una empresa matriculada o créala con el botón +. Los datos se copian a esta cotización.')
            ->icon(Heroicon::OutlinedBuildingOffice2)
            ->compact()
            ->schema([
                Select::make('client_id')
                    ->label('Empresa / cliente')
                    ->relationship(
                        name: 'client',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->orderByRaw('COALESCE(NULLIF(company, ""), name)'),
                    )
                    ->getOptionLabelFromRecordUsing(fn (Client $record): string => $record->label)
                    ->searchable(['company', 'name', 'email', 'phone', 'document'])
                    ->preload()
                    ->nullable()
                    ->live()
                    ->createOptionForm([
                        Grid::make(2)->schema(ClientForm::modalFields()),
                    ])
                    ->createOptionModalHeading('Matricular cliente')
                    ->editOptionForm([
                        Grid::make(2)->schema(ClientForm::modalFields()),
                    ])
                    ->afterStateUpdated(function ($state, Set $set): void {
                        if (blank($state)) {
                            return;
                        }

                        $client = Client::query()->find($state);
                        if (! $client) {
                            return;
                        }

                        foreach ($client->quoteAttributes() as $field => $value) {
                            if (filled($value)) {
                                $set($field, $value);
                            }
                        }
                    })
                    ->helperText('Al elegir una empresa se prellenan contacto, teléfono, correo y dirección.')
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->label('Contacto')
                    ->required()
                    ->maxLength(100),
                TextInput::make('company')
                    ->label('Empresa')
                    ->maxLength(120),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->required()
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->maxLength(100),
                Select::make('service_selection')
                    ->label('Servicio')
                    ->options(fn (): array => self::serviceOptions())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Select $component): void {
                        $saved = $component->getRecord()?->service;
                        if (blank($saved)) {
                            return;
                        }

                        $component->state(self::isCatalogService($saved) ? $saved : self::OTHER_SERVICE);
                    })
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                        if ($state === self::OTHER_SERVICE) {
                            if (self::isCatalogService($get('service'))) {
                                $set('service', '');
                            }

                            return;
                        }

                        if (filled($state)) {
                            $set('service', $state);
                        }
                    })
                    ->helperText('Elige un servicio del catálogo. Si no está, usa “Otro servicio” y escríbelo.'),
                TextInput::make('service')
                    ->label('¿Cuál servicio?')
                    ->placeholder('Escribe el servicio')
                    ->required(fn (Get $get): bool => $get('service_selection') === self::OTHER_SERVICE)
                    ->maxLength(120)
                    ->visible(fn (Get $get): bool => $get('service_selection') === self::OTHER_SERVICE)
                    ->dehydratedWhenHidden(),
                TextInput::make('project_title')
                    ->label('Proyecto / obra')
                    ->placeholder('Ej.: Colegio Cola de El Zorro — cableado')
                    ->maxLength(180),
                TextInput::make('zone')
                    ->label('Zona')
                    ->maxLength(80),
                TextInput::make('client_address')
                    ->label('Dirección')
                    ->maxLength(255),
                Textarea::make('message')
                    ->label('Necesidad del cliente')
                    ->rows(2)
                    ->columnSpanFull(),
                Section::make('Visita técnica')
                    ->compact()
                    ->collapsed()
                    ->schema([
                        Select::make('intent')
                            ->label('Intención')
                            ->options(Quote::INTENTS)
                            ->required()
                            ->default('info'),
                        DatePicker::make('preferred_visit_date')
                            ->label('Fecha preferida')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Select::make('preferred_visit_slot')
                            ->label('Franja')
                            ->options(Quote::VISIT_SLOTS)
                            ->placeholder('—'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->columnSpan([
                'default' => 1,
                'xl' => 8,
            ]);
    }

    protected static function summarySection(): Section
    {
        return Section::make('Resumen')
            ->icon(Heroicon::OutlinedCalculator)
            ->compact()
            ->secondary()
            ->schema([
                Placeholder::make('quote_running_totals')
                    ->hiddenLabel()
                    ->content(fn (Get $get): HtmlString => self::totalsHtml($get)),
                DatePicker::make('valid_until')
                    ->label('Válida hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
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
                TextInput::make('advisor_name')
                    ->label('Asesor')
                    ->maxLength(120),
                TextInput::make('advisor_title')
                    ->label('Cargo')
                    ->default('Asesor comercial')
                    ->maxLength(120),
            ])
            ->columnSpan([
                'default' => 1,
                'xl' => 4,
            ]);
    }

    protected static function itemsSection(): Section
    {
        return Section::make('Ítems')
            ->description('Una fila por concepto. Elige un frecuente o escribe uno nuevo; se guarda en Conceptos comerciales.')
            ->icon(Heroicon::OutlinedQueueList)
            ->compact()
            ->schema([
                Repeater::make('items')
                    ->relationship()
                    ->hiddenLabel()
                    ->orderColumn('sort_order')
                    ->reorderable()
                    ->defaultItems(0)
                    ->compact()
                    ->live()
                    ->cloneable()
                    ->table([
                        TableColumn::make('Catálogo')->width('13rem'),
                        TableColumn::make('Concepto')->markAsRequired()->width('12rem'),
                        TableColumn::make('Descripción'),
                        TableColumn::make('Cant.')->width('5.5rem'),
                        TableColumn::make('Und.')->width('7rem'),
                        TableColumn::make('Vr. unit.')->width('8rem'),
                        TableColumn::make('Desc.')->width('5.5rem'),
                        TableColumn::make('IVA')->width('5.5rem'),
                        TableColumn::make('Total')->width('8rem'),
                    ])
                    ->schema([
                        Select::make('quote_catalog_item_id')
                            ->hiddenLabel()
                            ->options(fn () => QuoteCatalogItem::active()
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Manual')
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
                            }),
                        TextInput::make('concept')
                            ->hiddenLabel()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Concepto'),
                        TextInput::make('description')
                            ->hiddenLabel()
                            ->required()
                            ->maxLength(1000)
                            ->placeholder('Detalle para el PDF'),
                        TextInput::make('quantity')
                            ->hiddenLabel()
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(0.01)
                            ->live(),
                        Select::make('unit')
                            ->hiddenLabel()
                            ->options(QuoteCatalogItem::UNITS)
                            ->default('unidad')
                            ->required(),
                        TextInput::make('unit_price')
                            ->hiddenLabel()
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->minValue(0)
                            ->live(),
                        TextInput::make('discount_percent')
                            ->hiddenLabel()
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->dehydrateStateUsing(fn ($state): float => filled($state) ? (float) $state : 0)
                            ->live(),
                        TextInput::make('tax_rate')
                            ->hiddenLabel()
                            ->numeric()
                            ->suffix('%')
                            ->default(19)
                            ->minValue(0)
                            ->maxValue(100)
                            ->dehydrateStateUsing(fn ($state): float => filled($state) ? (float) $state : 0)
                            ->live(),
                        Placeholder::make('calculated_total')
                            ->hiddenLabel()
                            ->content(function (Get $get): string {
                                $gross = (float) ($get('quantity') ?: 0) * (float) ($get('unit_price') ?: 0);
                                $net = $gross * (1 - ((float) ($get('discount_percent') ?: 0) / 100));
                                $total = $net * (1 + ((float) ($get('tax_rate') ?: 0) / 100));

                                return '$'.number_format($total, 0, ',', '.');
                            }),
                        Hidden::make('type')->default('product'),
                        Hidden::make('code'),
                    ])
                    ->addActionLabel('Agregar ítem')
                    ->columnSpanFull(),
            ])
            ->columnSpanFull();
    }

    protected static function termsSection(): Section
    {
        return Section::make('Condiciones')
            ->icon(Heroicon::OutlinedDocumentCheck)
            ->compact()
            ->collapsed()
            ->schema([
                Textarea::make('terms')
                    ->label('Condiciones comerciales')
                    ->rows(3)
                    ->default(fn () => config('quotes.default_terms'))
                    ->dehydrateStateUsing(fn ($state): ?string => blank($state) || trim((string) $state) === '0'
                        ? null
                        : trim((string) $state))
                    ->columnSpanFull(),
                Textarea::make('payment_terms')
                    ->label('Forma de pago')
                    ->rows(2)
                    ->default(fn () => config('quotes.default_payment_terms'))
                    ->dehydrateStateUsing(fn ($state): ?string => blank($state) || trim((string) $state) === '0'
                        ? null
                        : trim((string) $state)),
                Textarea::make('warranty_terms')
                    ->label('Garantía')
                    ->rows(2)
                    ->default(fn () => config('quotes.default_warranty_terms'))
                    ->dehydrateStateUsing(fn ($state): ?string => blank($state) || trim((string) $state) === '0'
                        ? null
                        : trim((string) $state)),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    protected static function followUpSection(): Section
    {
        return Section::make('Seguimiento interno')
            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
            ->compact()
            ->collapsed()
            ->schema([
                Textarea::make('notes')
                    ->label('Notas internas')
                    ->rows(2)
                    ->columnSpanFull(),
                TextInput::make('price_min')
                    ->label('Rango mín. (legacy)')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('price_max')
                    ->label('Rango máx. (legacy)')
                    ->numeric()
                    ->prefix('$'),
            ])
            ->columns(2)
            ->columnSpanFull();
    }

    protected static function totalsHtml(Get $get): HtmlString
    {
        $items = collect($get('items') ?? []);
        $subtotal = 0.0;
        $discount = 0.0;
        $tax = 0.0;
        $total = 0.0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $line = QuoteLineCalculator::calculate(
                (float) ($item['quantity'] ?? 0),
                (float) ($item['unit_price'] ?? 0),
                (float) ($item['discount_percent'] ?? 0),
                (float) ($item['tax_rate'] ?? 0),
            );

            $subtotal += $line['gross_subtotal'];
            $discount += $line['discount_amount'];
            $tax += $line['tax_amount'];
            $total += $line['line_total'];
        }

        $count = $items->filter(fn ($item) => is_array($item) && filled($item['concept'] ?? null))->count();
        $money = static fn (float $value): string => '$'.number_format($value, 0, ',', '.');
        $row = static function (string $label, string $value, bool $strong = false): string {
            $weight = $strong ? '700' : '500';
            $size = $strong ? '15px' : '13px';
            $color = $strong ? '#0f766e' : '#334155';

            return '<div style="display:flex;justify-content:space-between;gap:12px;align-items:baseline;padding:6px 0;">'
                .'<span style="color:#64748b;font-size:12px;">'.e($label).'</span>'
                .'<span style="font-weight:'.$weight.';font-size:'.$size.';color:'.$color.';">'.e($value).'</span>'
                .'</div>';
        };

        return new HtmlString(
            '<div style="border:1px solid #99f6e4;background:#f0fdfa;border-radius:12px;padding:12px 14px;margin-bottom:4px;">'
            .'<div style="font-size:11px;letter-spacing:.04em;text-transform:uppercase;color:#0f766e;font-weight:700;margin-bottom:8px;">'
            .e($count).' ítem'.($count === 1 ? '' : 's')
            .'</div>'
            .$row('Subtotal', $money($subtotal))
            .$row('Descuentos', $money($discount))
            .$row('IVA', $money($tax))
            .'<div style="border-top:1px solid #99f6e4;margin:4px 0;"></div>'
            .$row('Total', $money($total).' COP', true)
            .'<p style="margin:8px 0 0;font-size:11px;color:#94a3b8;line-height:1.35;">Se actualiza al cambiar cantidades, precios, descuentos o IVA.</p>'
            .'</div>'
        );
    }
}
