<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identidad')
                ->description('Queda en el padrón para reutilizarlo en cotizaciones, facturas y cuentas de cobro.')
                ->icon('heroicon-o-building-office-2')
                ->compact()
                ->schema(self::identityFields())
                ->columns(2),
            Section::make('Datos fiscales')
                ->description('Necesarios al emitir factura electrónica. Puedes completarlos después.')
                ->icon('heroicon-o-identification')
                ->compact()
                ->collapsed()
                ->schema(self::fiscalFields())
                ->columns(2),
        ]);
    }

    /**
     * Campos reutilizados en el alta rápida desde una cotización.
     *
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function identityFields(): array
    {
        return [
            TextInput::make('company')
                ->label('Empresa / razón social')
                ->maxLength(180)
                ->placeholder('Ej.: Hotel Selis S.A.S.'),
            TextInput::make('name')
                ->label('Contacto')
                ->required()
                ->maxLength(120)
                ->placeholder('Nombre de quien recibe la cotización'),
            TextInput::make('phone')
                ->label('Teléfono / WhatsApp')
                ->tel()
                ->maxLength(40),
            TextInput::make('email')
                ->label('Correo')
                ->email()
                ->maxLength(120),
            TextInput::make('address')
                ->label('Dirección')
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('zone')
                ->label('Zona / municipio')
                ->maxLength(80),
            Toggle::make('is_active')
                ->label('Activo')
                ->default(true),
            Textarea::make('notes')
                ->label('Notas internas')
                ->rows(2)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function fiscalFields(): array
    {
        return [
            Select::make('document_type')
                ->label('Tipo de documento')
                ->options(Client::DOCUMENT_TYPES)
                ->placeholder('—')
                ->live(),
            TextInput::make('document')
                ->label('NIT / documento')
                ->maxLength(40),
            TextInput::make('dv')
                ->label('DV')
                ->maxLength(2)
                ->visible(fn ($get) => $get('document_type') === '31'),
            TextInput::make('city_code')
                ->label('Código ciudad')
                ->default('05001')
                ->maxLength(10)
                ->helperText('DIVIPOLA. Medellín = 05001.'),
            TextInput::make('dept_code')
                ->label('Código departamento')
                ->default('05')
                ->maxLength(10),
        ];
    }

    /**
     * Alta rápida desde el select de cotización.
     *
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function modalFields(): array
    {
        return [
            TextInput::make('company')
                ->label('Empresa / razón social')
                ->maxLength(180)
                ->columnSpanFull(),
            TextInput::make('name')
                ->label('Contacto')
                ->required()
                ->maxLength(120),
            TextInput::make('phone')
                ->label('Teléfono / WhatsApp')
                ->tel()
                ->maxLength(40),
            TextInput::make('email')
                ->label('Correo')
                ->email()
                ->maxLength(120),
            TextInput::make('address')
                ->label('Dirección')
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('zone')
                ->label('Zona / municipio')
                ->maxLength(80),
            Select::make('document_type')
                ->label('Tipo de documento')
                ->options(Client::DOCUMENT_TYPES)
                ->default('31')
                ->live(),
            TextInput::make('document')
                ->label('NIT / documento')
                ->maxLength(40),
            TextInput::make('dv')
                ->label('DV')
                ->maxLength(2)
                ->visible(fn ($get) => $get('document_type') === '31'),
        ];
    }
}
