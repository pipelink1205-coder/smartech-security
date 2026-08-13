<?php

namespace App\Filament\Resources\DianResolutions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DianResolutionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Resolución de numeración')
                ->description('Prefijo, rango y vigencia que autorizó la DIAN. Solo una resolución puede estar activa por ambiente.')
                ->icon(Heroicon::OutlinedDocumentCheck)
                ->compact()
                ->columns(3)
                ->schema([
                    Select::make('environment')
                        ->label('Ambiente')
                        ->options([
                            2 => 'Habilitación / pruebas (2)',
                            1 => 'Producción (1)',
                        ])
                        ->required()
                        ->default(2),
                    TextInput::make('numero_resolucion')
                        ->label('Número de resolución')
                        ->required()
                        ->maxLength(30),
                    DatePicker::make('fecha_resolucion')
                        ->label('Fecha de resolución')
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                    TextInput::make('prefijo')
                        ->label('Prefijo')
                        ->required()
                        ->maxLength(10)
                        ->default('SETP')
                        ->helperText('En habilitación suele ser SETP. En producción, el de tu resolución.'),
                    TextInput::make('rango_desde')
                        ->label('Desde')
                        ->numeric()
                        ->required()
                        ->minValue(1),
                    TextInput::make('rango_hasta')
                        ->label('Hasta')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->gt('rango_desde'),
                    DatePicker::make('vigencia_desde')
                        ->label('Vigencia desde')
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                    DatePicker::make('vigencia_hasta')
                        ->label('Vigencia hasta')
                        ->native(false)
                        ->displayFormat('d/m/Y'),
                    TextInput::make('clave_tecnica')
                        ->label('Clave técnica')
                        ->password()
                        ->revealable()
                        ->maxLength(150)
                        ->helperText('La clave técnica de esta resolución. Se usa en el CUFE.'),
                    TextInput::make('consecutivo_actual')
                        ->label('Último número usado')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->helperText('0 = el próximo emitido será el inicio del rango.'),
                    Toggle::make('is_active')
                        ->label('Activa')
                        ->default(true)
                        ->helperText('Al activarla se desactivan las demás del mismo ambiente.')
                        ->inline(false),
                ]),
        ]);
    }
}
