<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Support\Filament\PublicAssetUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Servicio')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, ?string $state, ?string $old, $get): void {
                                if (blank($get('slug'))) {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Identificador')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('icon')
                            ->label('Icono (emoji)')
                            ->required()
                            ->maxLength(10)
                            ->helperText('Ejemplo: 📹 🚨 ☀️'),
                        TextInput::make('highlight')
                            ->label('Frase destacada')
                            ->maxLength(80)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        TagsInput::make('features')
                            ->label('Características (lista)')
                            ->placeholder('Escribir y Enter')
                            ->columnSpanFull(),
                        PublicAssetUpload::image('image', 'images/services')
                            ->label('Imagen del servicio')
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('price_from')
                                    ->label('Precio desde (COP)')
                                    ->numeric()
                                    ->prefix('$'),
                                TextInput::make('order')
                                    ->label('Orden en la página')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label('Visible en el sitio')
                                    ->default(true),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }
}
