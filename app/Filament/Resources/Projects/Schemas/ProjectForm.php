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
                        ->label('Servicio')
                        ->relationship('service', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(1),
                    Textarea::make('description')
                        ->label('Descripción en el sitio')
                        ->rows(3)
                        ->placeholder('Qué se instaló y qué resultado tuvo el cliente.')
                        ->columnSpanFull(),
                    TextInput::make('location')
                        ->label('Zona / municipio')
                        ->maxLength(100)
                        ->placeholder('El Poblado, Envigado…'),
                    TextInput::make('year')
                        ->label('Año')
                        ->numeric()
                        ->minValue(2000)
                        ->maxValue(2100),
                    Toggle::make('is_featured')
                        ->label('Mostrar en la página de inicio')
                        ->inline(false)
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
                    . 'Sube evidencias del trabajo. Después de guardar, en <strong>Galería de fotos</strong> '
                    . 'elige la portada (estrella), ordena y edita descripciones.'
                    . '</p>'
                ))
                ->columnSpanFull(),
            PublicAssetUpload::image('pending_gallery', 'images/projects')
                ->label('Imágenes')
                ->multiple()
                ->reorderable()
                ->appendFiles()
                ->helperText('La primera imagen subida será portada si aún no hay otra marcada.')
                ->columnSpanFull(),
        ];
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function mapaTab(): array
    {
        return [
            TextInput::make('address')
                ->label('Dirección para visitantes')
                ->maxLength(255)
                ->placeholder('Carrera 72 # 11-11, Laureles')
                ->helperText('Texto corto en el sitio. La búsqueda del mapa no lo modifica.')
                ->columnSpanFull(),

            View::make('filament.forms.project-location-picker')
                ->columnSpanFull(),

            Grid::make(3)
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
                    TextInput::make('comuna_numero')
                        ->label('Comuna (1–16)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(16)
                        ->helperText('Solo Medellín. Fuera: vacío.'),
                ]),
        ];
    }
}
