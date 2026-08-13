<?php

namespace App\Filament\Resources\CollectionAccounts\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CollectionAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Documento')
                ->columns(3)
                ->schema([
                    TextInput::make('number')
                        ->label('Número')
                        ->disabled(),
                    Select::make('status')
                        ->label('Estado')
                        ->options(\App\Models\CollectionAccount::STATUSES)
                        ->required(),
                    TextInput::make('concept')
                        ->label('Concepto general')
                        ->maxLength(255),
                ]),

            Section::make('Cliente')
                ->columns(2)
                ->schema([
                    TextInput::make('client_name')
                        ->label('Nombre / empresa')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('client_document')
                        ->label('Documento / NIT')
                        ->maxLength(40),
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
                ]),

            Section::make('Ítems')
                ->schema([
                    Repeater::make('items')
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
                                ->minValue(0.01)
                                ->live(onBlur: true),
                            TextInput::make('unit_price')
                                ->label('Valor unitario')
                                ->numeric()
                                ->prefix('$')
                                ->required()
                                ->minValue(0)
                                ->live(onBlur: true),
                        ])
                        ->columns(4)
                        ->addActionLabel('Agregar ítem')
                        ->collapsible(),
                ]),

            Section::make('Datos bancarios')
                ->columns(2)
                ->schema([
                    TextInput::make('bank_account_holder')->label('Titular')->maxLength(255),
                    TextInput::make('bank_nit')->label('NIT titular')->maxLength(30),
                    TextInput::make('bank_name')->label('Banco')->maxLength(255),
                    TextInput::make('bank_account_type')->label('Tipo de cuenta')->maxLength(30),
                    TextInput::make('bank_account_number')->label('Número de cuenta')->maxLength(40),
                    Textarea::make('payment_instructions')
                        ->label('Instrucciones de pago')
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('notes')
                        ->label('Observaciones')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Totales')
                ->columns(4)
                ->schema([
                    TextInput::make('subtotal')->label('Subtotal')->prefix('$')->numeric()->disabled(),
                    TextInput::make('discount_total')->label('Descuentos')->prefix('$')->numeric()->disabled(),
                    TextInput::make('tax_total')->label('IVA')->prefix('$')->numeric()->disabled(),
                    TextInput::make('total')->label('Total')->prefix('$')->numeric()->disabled(),
                ]),
        ]);
    }
}
