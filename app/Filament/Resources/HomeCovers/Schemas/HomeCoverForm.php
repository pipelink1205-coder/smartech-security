<?php

namespace App\Filament\Resources\HomeCovers\Schemas;

use App\Models\HomeCover;
use App\Support\Filament\PublicAssetUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class HomeCoverForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaults = HomeCover::fromSiteConfig();

        return $schema
            ->components([
                Placeholder::make('guia_portada')
                    ->label('')
                    ->content(new HtmlString(
                        '<p class="text-sm text-gray-600 dark:text-gray-400">'
                        .'<strong>Respaldo institucional</strong> se muestra cuando no hay una campaña vigente. '
                        .'Una <strong>campaña, promoción o temporada</strong> publicada y dentro de fechas gana automáticamente. '
                        .'Si dos coinciden, gana la de mayor prioridad.'
                        .'</p>'
                    ))
                    ->columnSpanFull(),

                Tabs::make('Portada')
                    ->tabs([
                        Tab::make('Identidad')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->schema(self::identidadTab()),
                        Tab::make('Textos')
                            ->icon(Heroicon::OutlinedDocumentText)
                            ->schema(self::textosTab($defaults)),
                        Tab::make('Imagen')
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema(self::imagenTab()),
                        Tab::make('Botones y franja')
                            ->icon(Heroicon::OutlinedCursorArrowRays)
                            ->schema(self::accionesTab($defaults)),
                        Tab::make('Publicación')
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->schema(self::publicacionTab()),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function identidadTab(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('name')
                    ->label('Nombre interno')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Solo lo ves en el admin. Ej: Campaña Día de la Madre 2026.')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($set, ?string $state, $get): void {
                        if (blank($get('slug'))) {
                            $set('slug', Str::slug($state ?? ''));
                        }
                    }),
                TextInput::make('slug')
                    ->label('Identificador')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Único. Sirve para duplicar y para la vista previa.'),
                Select::make('kind')
                    ->label('Tipo de edición')
                    ->options(HomeCover::KINDS)
                    ->required()
                    ->default(HomeCover::KIND_AIRE)
                    ->helperText('Clasifica el motivo del cambio: institucional, campaña, promoción, temporada o solo aire nuevo.'),
            ]),
        ];
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function textosTab(HomeCover $defaults): array
    {
        return [
            TextInput::make('badge')
                ->label('Piloto / badge')
                ->maxLength(120)
                ->default($defaults->badge)
                ->placeholder('+500 Proyectos exitosos'),
            TextInput::make('title')
                ->label('Titular')
                ->required()
                ->maxLength(180)
                ->default($defaults->title)
                ->helperText('El H1 de la home. Mantén el tono de marca.'),
            Textarea::make('subtitle')
                ->label('Bajada')
                ->rows(4)
                ->default($defaults->subtitle)
                ->columnSpanFull(),
            Repeater::make('stats')
                ->label('Cifras del hero')
                ->schema([
                    TextInput::make('num')->label('Cifra')->required()->maxLength(12),
                    TextInput::make('label')->label('Etiqueta')->required()->maxLength(60),
                ])
                ->default($defaults->stats)
                ->defaultItems(0)
                ->maxItems(4)
                ->columns(2)
                ->helperText('Vacío = se usan las cifras institucionales. Máximo 4.')
                ->columnSpanFull(),
        ];
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function imagenTab(): array
    {
        return [
            PublicAssetUpload::image('image', 'images/portadas', watermark: false)
                ->label('Foto de portada')
                ->helperText('Horizontal, mínimo 1600 px de ancho. Sin marca de agua: es pieza de campaña.')
                ->columnSpanFull(),
            TextInput::make('image_alt')
                ->label('Texto alternativo')
                ->maxLength(180)
                ->default('Instalación de cámaras de seguridad y sistemas tecnológicos en Medellín')
                ->helperText('Describe la foto para accesibilidad y SEO.'),
        ];
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function accionesTab(HomeCover $defaults): array
    {
        return [
            Section::make('Botones')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('cta_primary_label')
                            ->label('Botón principal')
                            ->required()
                            ->default($defaults->cta_primary_label),
                        TextInput::make('cta_primary_url')
                            ->label('Destino principal')
                            ->required()
                            ->default($defaults->cta_primary_url)
                            ->helperText('Ancla (#servicios) o URL completa. #servicios y #contacto siguen el scroll del sitio.'),
                        TextInput::make('cta_secondary_label')
                            ->label('Botón secundario')
                            ->required()
                            ->default($defaults->cta_secondary_label),
                        TextInput::make('cta_secondary_url')
                            ->label('Destino secundario')
                            ->required()
                            ->default($defaults->cta_secondary_url)
                            ->helperText('Usa /?intent=visit#contacto para abrir el formulario de visita.'),
                    ]),
                ]),
            Section::make('Franja bajo la portada')
                ->schema([
                    TextInput::make('strip_label')
                        ->label('Frase de la franja')
                        ->default($defaults->strip_label)
                        ->maxLength(120),
                    TagsInput::make('strip_highlights')
                        ->label('Destacados')
                        ->placeholder('Escribir y Enter')
                        ->default($defaults->strip_highlights)
                        ->helperText('Vacío = se usan los destacados institucionales.'),
                ]),
        ];
    }

    /** @return list<\Filament\Schemas\Components\Component> */
    protected static function publicacionTab(): array
    {
        return [
            Grid::make(2)->schema([
                Toggle::make('is_published')
                    ->label('Publicada')
                    ->helperText('Si está apagada, nadie la ve (salvo vista previa).')
                    ->default(false),
                Toggle::make('is_fallback')
                    ->label('Respaldo institucional')
                    ->helperText('Solo una. Es la portada cuando no hay campaña vigente.')
                    ->default(false),
                TextInput::make('priority')
                    ->label('Prioridad')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99)
                    ->default(10)
                    ->helperText('Si dos campañas coinciden, gana el número más alto.'),
                DateTimePicker::make('starts_at')
                    ->label('Desde')
                    ->seconds(false)
                    ->native(false)
                    ->helperText('Vacío = empieza en cuanto esté publicada.'),
                DateTimePicker::make('ends_at')
                    ->label('Hasta')
                    ->seconds(false)
                    ->native(false)
                    ->afterOrEqual('starts_at')
                    ->helperText('Vacío = no vence. Al cumplirse vuelve el respaldo.'),
            ]),
        ];
    }
}
