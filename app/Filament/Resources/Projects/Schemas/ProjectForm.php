<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Support\Filament\PublicAssetUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información general')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($set, ?string $state, ?string $old, $get): void {
                                if (blank($get('slug'))) {
                                    $set('slug', Str::slug($state ?? ''));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Identificador (URL)')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Se genera del título si lo dejas vacío.'),
                        Select::make('category')
                            ->label('Categoría / servicio')
                            ->options([
                                'Seguridad Corporativa' => 'Seguridad Corporativa',
                                'IPTV Hotelera' => 'IPTV Hotelera',
                                'Energía Solar' => 'Energía Solar',
                                'Control de Acceso' => 'Control de Acceso',
                                'Redes Empresariales' => 'Redes Empresariales',
                                'Domótica' => 'Domótica',
                                'Alarmas' => 'Alarmas',
                                'Cámaras y Videovigilancia' => 'Cámaras y Videovigilancia',
                            ])
                            ->searchable()
                            ->required(),
                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4)
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('location')
                                    ->label('Zona / ciudad')
                                    ->maxLength(100)
                                    ->helperText('Ej: El Poblado, Envigado'),
                                TextInput::make('year')
                                    ->label('Año del trabajo')
                                    ->numeric()
                                    ->minValue(2000)
                                    ->maxValue(2100),
                            ]),
                        Toggle::make('is_featured')
                            ->label('Destacado en la página de inicio')
                            ->default(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Fotos del proyecto')
                    ->description('Todas las fotos que verán los visitantes en la galería del sitio.')
                    ->schema([
                        Placeholder::make('fotos_ayuda')
                            ->label('')
                            ->content(new HtmlString(
                                '<ul class="list-disc ps-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">'
                                . '<li><strong>Aquí subes todas las fotos</strong> (evidencias del trabajo).</li>'
                                . '<li>La <strong>primera</strong> de la lista es la portada en el sitio.</li>'
                                . '<li>Después de guardar, en la pestaña <strong>Galería de fotos</strong> puedes añadir más, editar textos o cambiar el orden.</li>'
                                . '</ul>'
                            ))
                            ->columnSpanFull(),
                        PublicAssetUpload::image('pending_gallery', 'images/projects')
                            ->label('Subir fotos')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->columnSpanFull(),
                    ]),

                Section::make('Ubicación en el mapa del sitio')
                    ->description('Marca dónde quedó instalado el trabajo para que aparezca en el mapa público.')
                    ->schema([
                        TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255)
                            ->placeholder('Ej: Carrera 72 # 11-11, Medellín')
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
                                    ->label('Comuna Medellín (1–16)')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(16)
                                    ->helperText('Solo si el trabajo está en Medellín. Fuera de la ciudad, déjalo vacío.'),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }
}
