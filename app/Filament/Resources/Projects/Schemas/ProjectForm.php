<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Support\Filament\PublicAssetUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('guia_proyecto')
                    ->label('')
                    ->content(new HtmlString(
                        '<p class="text-sm text-gray-600 dark:text-gray-400 mb-1">'
                        . '<strong>Datos</strong> → información del trabajo · '
                        . '<strong>Fotos</strong> → evidencias · '
                        . '<strong>Mapa</strong> → pin en el sitio público.'
                        . '</p>'
                    ))
                    ->columnSpanFull(),

                Tabs::make('Proyecto')
                    ->tabs([
                        Tab::make('Datos')
                            ->icon(Heroicon::OutlinedDocumentText)
                            ->schema(self::datosTab()),

                        Tab::make('Fotos')
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema(self::fotosTab()),

                        Tab::make('Mapa')
                            ->icon(Heroicon::OutlinedMapPin)
                            ->schema(self::mapaTab()),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function datosTab(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Nombre del proyecto')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ej: Hotel Boutique El Poblado')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($set, ?string $state, $get): void {
                            if (blank($get('slug'))) {
                                $set('slug', Str::slug($state ?? ''));
                            }
                        })
                        ->columnSpan(1),
                    Select::make('service_id')
                        ->label('Tipo de servicio')
                        ->relationship('service', 'name', fn ($query) => $query->orderBy('order'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Clasifica el proyecto bajo un servicio. Aparecerá en la subpágina de ese servicio y en el filtro del portafolio.')
                        ->columnSpan(1),
                    Textarea::make('description')
                        ->label('Descripción en el sitio')
                        ->rows(3)
                        ->placeholder('Qué se instaló y qué resultado tuvo el cliente.')
                        ->columnSpanFull(),
                    TextInput::make('year')
                        ->label('Año')
                        ->numeric()
                        ->minValue(2000)
                        ->maxValue(2100),
                    Toggle::make('is_featured')
                        ->label('Mostrar en la página de inicio')
                        ->inline(false)
                        ->columnSpanFull(),

                    Section::make('Logo del cliente')
                        ->description('Opcional. Se puede mostrar en la banda “Empresas que confían” del inicio.')
                        ->schema([
                            PublicAssetUpload::image('client_logo', 'images/clients/from-projects', watermark: false)
                                ->label('Logo')
                                ->imageEditor(false)
                                ->helperText('PNG o JPG preferible con fondo transparente o blanco. Sin marca de agua.')
                                ->columnSpanFull(),
                            Toggle::make('show_in_clients_ticker')
                                ->label('Mostrar en “Empresas que confían” del inicio')
                                ->helperText('Solo aplica si hay logo cargado.')
                                ->inline(false),
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Avanzado')
                ->description('Solo si necesitas cambiar la URL interna del proyecto.')
                ->collapsed()
                ->schema([
                    TextInput::make('slug')
                        ->label('Identificador (URL)')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                ]),
        ];
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function fotosTab(): array
    {
        return [
            Placeholder::make('fotos_resumen')
                ->label('')
                ->content(new HtmlString(
                    '<p class="text-sm text-gray-600 dark:text-gray-400">'
                    . '<strong>La primera foto es la portada.</strong> Las fotos nuevas se agregan al final; '
                    . 'arrastra para cambiar el orden o la portada. Se guardan al guardar el proyecto.'
                    . '</p>'
                ))
                ->columnSpanFull(),
            PublicAssetUpload::image('gallery', 'images/projects')
                ->label('Fotos del proyecto')
                ->multiple()
                ->reorderable()
                ->appendFiles()
                ->panelLayout('grid')
                ->maxFiles(12)
                ->helperText('JPG, PNG o WebP · hasta 12 · nuevas al final · la 1ª es portada · se respeta si es horizontal o vertical · marca de agua al subir. Recorta en el teléfono si lo necesitas (el editor interno está desactivado para no deformar).')
                ->columnSpanFull(),
        ];
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function mapaTab(): array
    {
        return [
            TextInput::make('address')
                ->label('Dirección del proyecto')
                ->maxLength(255)
                ->placeholder('Carrera 72 # 11-11, Laureles')
                ->helperText('Escríbela y pulsa "Ubicar pin". En Medellín se usa la API oficial de la Alcaldía; la comuna y el barrio se completan solos.')
                ->columnSpanFull(),

            View::make('filament.forms.project-location-picker')
                ->columnSpanFull(),

            Grid::make(3)
                ->schema([
                    TextInput::make('location')
                        ->label('Zona detectada')
                        ->readOnly()
                        ->dehydrated()
                        ->placeholder('Se autocompleta con el mapa'),
                    TextInput::make('comuna_numero')
                        ->label('Comuna (1–16)')
                        ->numeric()
                        ->readOnly()
                        ->dehydrated()
                        ->placeholder('Solo Medellín'),
                    TextInput::make('barrio')
                        ->label('Barrio')
                        ->readOnly()
                        ->dehydrated()
                        ->placeholder('Se autocompleta'),
                ]),

            Section::make('Coordenadas (avanzado)')
                ->description('Se completan solas. Ajústalas solo si el pin no cae en el sitio exacto.')
                ->collapsed()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('latitude')
                                ->label('Latitud')
                                ->numeric()
                                ->step(0.000001)
                                ->live(debounce: 500),
                            TextInput::make('longitude')
                                ->label('Longitud')
                                ->numeric()
                                ->step(0.000001)
                                ->live(debounce: 500),
                        ]),
                ]),
        ];
    }
}
