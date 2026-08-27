<?php

namespace App\Filament\Resources\WhatsappLeads\Schemas;

use App\Models\Service;
use App\Models\WhatsappLead;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WhatsappLeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contacto')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('phone')
                        ->label('Teléfono / WhatsApp')
                        ->tel()
                        ->required()
                        ->maxLength(20),
                    Select::make('service')
                        ->label('Servicio de interés')
                        ->options(fn (): array => self::serviceOptions())
                        ->searchable()
                        ->required(),
                    Select::make('status')
                        ->label('Estado')
                        ->options(WhatsappLead::STATUSES)
                        ->required(),
                    Textarea::make('message')
                        ->label('Mensaje del visitante')
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('notes')
                        ->label('Notas internas')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
            Section::make('Origen en el sitio')
                ->columns(2)
                ->schema([
                    Placeholder::make('source_label')
                        ->label('Botón')
                        ->content(fn (?WhatsappLead $record): string => $record?->source_label ?? '—'),
                    Placeholder::make('click_count')
                        ->label('Clics')
                        ->content(fn (?WhatsappLead $record): string => (string) ($record?->click_count ?? 1)),
                    Placeholder::make('page_title')
                        ->label('Página')
                        ->content(fn (?WhatsappLead $record): string => $record?->page_title ?: '—'),
                    Placeholder::make('created_at')
                        ->label('Recibido')
                        ->content(fn (?WhatsappLead $record): string => $record?->created_at?->format('d/m/Y H:i') ?? '—'),
                    Placeholder::make('page_url')
                        ->label('URL')
                        ->content(fn (?WhatsappLead $record): string => $record?->page_url ?: '—')
                        ->columnSpanFull(),
                    Placeholder::make('quote_link')
                        ->label('Cotización')
                        ->content(fn (?WhatsappLead $record): string => $record?->quote?->quote_number ?: 'Aún no convertida')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private static function serviceOptions(): array
    {
        $options = Service::query()
            ->active()
            ->ordered()
            ->pluck('name', 'name')
            ->all();

        $options['Varios servicios'] = 'Varios servicios';
        $options['No estoy seguro'] = 'No estoy seguro';

        return $options;
    }
}
