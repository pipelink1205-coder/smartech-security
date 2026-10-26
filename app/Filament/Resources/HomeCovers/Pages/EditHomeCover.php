<?php

namespace App\Filament\Resources\HomeCovers\Pages;

use App\Filament\Resources\HomeCovers\HomeCoverResource;
use App\Models\HomeCover;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class EditHomeCover extends EditRecord
{
    protected static string $resource = HomeCoverResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name'] ?? 'portada');
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Vista previa')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->url(fn (HomeCover $record): string => route('home', ['preview_portada' => $record->id]))
                ->openUrlInNewTab(),
            Action::make('duplicate')
                ->label('Duplicar')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (HomeCover $record) {
                    $copy = $record->replicateForEdition();

                    Notification::make()
                        ->title('Portada duplicada')
                        ->success()
                        ->send();

                    return redirect(HomeCoverResource::getUrl('edit', ['record' => $copy]));
                }),
            Action::make('publishNow')
                ->label('Publicar ahora')
                ->icon(Heroicon::OutlinedBolt)
                ->color('success')
                ->visible(fn (HomeCover $record): bool => ! $record->is_published || $record->starts_at?->isFuture())
                ->requiresConfirmation()
                ->modalHeading('Publicar esta portada ahora')
                ->modalDescription('Quedará publicada y, si tenía fecha futura, empezará de inmediato. El respaldo institucional sigue existiendo para cuando esta edición termine.')
                ->action(function (HomeCover $record): void {
                    $record->forceFill([
                        'is_published' => true,
                        'starts_at' => $record->starts_at?->isFuture() ? now() : $record->starts_at,
                    ])->save();

                    Notification::make()
                        ->title('Portada publicada')
                        ->success()
                        ->send();
                }),
            DeleteAction::make()
                ->disabled(fn (HomeCover $record): bool => $record->is_fallback)
                ->tooltip(fn (HomeCover $record): ?string => $record->is_fallback
                    ? 'No se puede borrar el respaldo institucional. Crea otro respaldo primero.'
                    : null),
        ];
    }
}
