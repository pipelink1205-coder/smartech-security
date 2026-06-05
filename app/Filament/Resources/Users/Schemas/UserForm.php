<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del usuario')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->required(fn ($livewire): bool => $livewire instanceof CreateRecord)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Dejar vacío al editar para no cambiar la contraseña.')
                            ->minLength(8)
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }
}
