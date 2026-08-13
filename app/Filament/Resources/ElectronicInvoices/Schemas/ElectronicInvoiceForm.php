<?php

namespace App\Filament\Resources\ElectronicInvoices\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ElectronicInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Estado DIAN')
                ->columns(3)
                ->schema([
                    TextInput::make('display_number')
                        ->label('Número')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(fn ($state, $record) => $record?->display_number),
                    TextInput::make('dian_status')
                        ->label('Estado')
                        ->disabled(),
                    TextInput::make('cufe')
                        ->label('CUFE')
                        ->disabled()
                        ->columnSpanFull(),
                    Textarea::make('dian_description')
                        ->label('Respuesta DIAN')
                        ->disabled()
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Adquiriente')
                ->description('Datos fiscales del cliente. Completa documento antes de emitir a DIAN.')
                ->columns(2)
                ->schema([
                    TextInput::make('client_name')
                        ->label('Nombre / razón social')
                        ->required()
                        ->maxLength(255),
                    Select::make('client_tipo_documento')
                        ->label('Tipo documento')
                        ->options([
                            '13' => 'CC (13)',
                            '31' => 'NIT (31)',
                            '22' => 'CE (22)',
                            '41' => 'Pasaporte (41)',
                            '42' => 'Documento extranjero (42)',
                        ])
                        ->required(),
                    TextInput::make('client_document')
                        ->label('Número documento')
                        ->maxLength(40)
                        ->helperText('Obligatorio para emisión DIAN.'),
                    TextInput::make('client_dv')
                        ->label('DV')
                        ->maxLength(2)
                        ->visible(fn ($get) => $get('client_tipo_documento') === '31'),
                    TextInput::make('client_email')
                        ->label('Correo')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('client_phone')
                        ->label('WhatsApp / teléfono')
                        ->tel()
                        ->maxLength(40),
                    TextInput::make('client_address')
                        ->label('Dirección')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('client_city_code')
                        ->label('Código ciudad (DIVIPOLA)')
                        ->default('05001')
                        ->maxLength(10),
                    TextInput::make('client_dept_code')
                        ->label('Código departamento')
                        ->default('05')
                        ->maxLength(10),
                ]),

            Section::make('Ítems')
                ->schema([
                    Repeater::make('details')
                        ->relationship()
                        ->label('')
                        ->schema([
                            TextInput::make('description')
                                ->label('Descripción')
                                ->required()
                                ->columnSpan(2),
                            TextInput::make('quantity')
                                ->label('Cant.')
                                ->numeric()
                                ->required()
                                ->minValue(0.01),
                            TextInput::make('price')
                                ->label('Precio c/IVA')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->minValue(0),
                        ])
                        ->columns(4)
                        ->addActionLabel('Agregar ítem')
                        ->collapsible(),
                ]),

            Section::make('Totales')
                ->columns(4)
                ->schema([
                    TextInput::make('subtotal')->label('Base')->prefix('$')->numeric()->disabled(),
                    TextInput::make('descuento_total')->label('Descuentos')->prefix('$')->numeric()->disabled(),
                    TextInput::make('iva')->label('IVA')->prefix('$')->numeric()->disabled(),
                    TextInput::make('total')->label('Total')->prefix('$')->numeric()->disabled(),
                ]),
        ]);
    }
}
