<?php

namespace App\Filament\Resources\Quotes\Schemas;

use App\Models\Quote;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cliente')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->disabled(),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->disabled(),
                        TextInput::make('email')
                            ->label('Correo')
                            ->disabled(),
                        TextInput::make('service')
                            ->label('Servicio solicitado')
                            ->disabled(),
                        TextInput::make('zone')
                            ->label('Zona')
                            ->disabled(),
                        Textarea::make('message')
                            ->label('Mensaje del cliente')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Seguimiento interno')
                    ->schema([
                        Select::make('status')
                            ->label('Estado')
                            ->options(Quote::STATUSES)
                            ->required(),
                        TextInput::make('price_min')
                            ->label('Precio mínimo (COP)')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('price_max')
                            ->label('Precio máximo (COP)')
                            ->numeric()
                            ->prefix('$'),
                        Textarea::make('notes')
                            ->label('Notas internas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
