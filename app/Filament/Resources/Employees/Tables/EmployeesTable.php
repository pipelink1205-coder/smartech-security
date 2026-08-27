<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->getStateUsing(function (Employee $record): ?string {
                        if (blank($record->photo_original) && blank($record->photo_card) && blank($record->photo_cutout)) {
                            return null;
                        }

                        return route('admin.employees.photo', $record);
                    })
                    ->checkFileExistence(false)
                    ->square()
                    ->size(40)
                    ->extraImgAttributes(['alt' => '']),
                TextColumn::make('employee_code')->label('Código')->searchable()->sortable(),
                TextColumn::make('full_name')
                    ->label('Empleado')
                    ->searchable(['first_names', 'last_names', 'position'])
                    ->sortable(['first_names'])
                    ->description(fn (Employee $record): ?string => $record->position),
                TextColumn::make('is_legal_representative')
                    ->label('Firma')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): ?string => $state ? 'Rep. legal' : null)
                    ->color('success')
                    ->placeholder(''),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Employee::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success', 'suspended' => 'warning', default => 'gray',
                    }),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('first_names')
            ->filters([
                SelectFilter::make('status')->label('Estado')->options(Employee::STATUSES),
                TernaryFilter::make('is_legal_representative')->label('Representante legal'),
            ])
            ->recordActions([
                Action::make('cardPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Employee $record): string => route('admin.employees.card-pdf', $record))
                    ->visible(fn (Employee $record): bool => $record->status === 'active'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
