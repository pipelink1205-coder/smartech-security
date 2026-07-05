<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Support\Filament\PublicAssetUpload;
use Filament\Forms\Components\Repeater;
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
                            ->label('Icono')
                            ->required()
                            ->maxLength(10)
                            ->helperText('Emoji o símbolo libre (ej. 📞 📹 🔥). En el sitio se muestra en formato plano; los emojis conocidos usan el SVG equivalente.')
                            ->columnSpanFull(),
                        TextInput::make('highlight')
                            ->label('Frase gancho')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Descripción corta (tarjeta)')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('long_description')
                            ->label('Descripción ampliada')
                            ->rows(8)
                            ->helperText('Párrafos separados por línea en blanco.')
                            ->columnSpanFull(),
                        TagsInput::make('features')
                            ->label('Características (tarjeta)')
                            ->placeholder('Escribir y Enter')
                            ->columnSpanFull(),
                        TagsInput::make('includes')
                            ->label('Qué incluye')
                            ->placeholder('Escribir y Enter')
                            ->columnSpanFull(),
                        TagsInput::make('process_steps')
                            ->label('Cómo lo hacemos (pasos)')
                            ->placeholder('Escribir y Enter')
                            ->columnSpanFull(),
                        TagsInput::make('brands')
                            ->label('Marcas')
                            ->placeholder('Escribir y Enter')
                            ->columnSpanFull(),
                        TagsInput::make('standards')
                            ->label('Normas y estándares')
                            ->placeholder('Escribir y Enter')
                            ->columnSpanFull(),
                        TagsInput::make('tools')
                            ->label('Herramientas')
                            ->placeholder('Escribir y Enter')
                            ->columnSpanFull(),
                        Repeater::make('faqs')
                            ->label('Preguntas frecuentes')
                            ->schema([
                                TextInput::make('question')->label('Pregunta')->required()->columnSpanFull(),
                                Textarea::make('answer')->label('Respuesta')->required()->rows(2)->columnSpanFull(),
                            ])
                            ->defaultItems(0)
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
