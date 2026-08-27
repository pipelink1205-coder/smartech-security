<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\Employees\Pages\Concerns\SavesDrawnSignature;
use App\Services\Employees\EmployeePhotoProcessor;
use App\Services\Employees\EmployeeSignatureStore;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Throwable;

class EditEmployee extends EditRecord
{
    use SavesDrawnSignature;

    protected static string $resource = EmployeeResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->persistDrawnSignature($data);
    }

    protected function afterSave(): void
    {
        $uploadChanged = $this->record->wasChanged('authorized_signature');
        $photoChanged = $this->record->wasChanged('photo_card') && ! $this->record->wasChanged('photo_cutout');
        $hadDrawn = filled($this->drawnSignature);
        $this->storeDrawnSignature();

        if ($uploadChanged || $hadDrawn) {
            $this->processSignature(notify: false);
        }

        if ($photoChanged) {
            $this->processPhoto();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Ver carnet')
                ->icon('heroicon-o-eye')
                ->url(fn (): string => route('admin.employees.card-preview', $this->record))
                ->openUrlInNewTab(),
            Action::make('download')
                ->label('Generar carnet')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (): string => route('admin.employees.card-pdf', $this->record)),
            Action::make('verification')
                ->label('Vista pública')
                ->icon('heroicon-o-qr-code')
                ->url(fn (): string => route('employees.verify', ['employee' => $this->record->verification_token]))
                ->openUrlInNewTab(),
            Action::make('processPhoto')
                ->label('Quitar fondo nuevamente')
                ->icon('heroicon-o-sparkles')
                ->requiresConfirmation()
                ->action(fn () => $this->processPhoto()),
            Action::make('processSignature')
                ->label('Quitar fondo de la firma')
                ->icon('heroicon-o-pencil-square')
                ->visible(fn (): bool => filled($this->record->authorized_signature))
                ->requiresConfirmation()
                ->modalHeading('Quitar el fondo de la firma')
                ->modalDescription('Se deja solo el trazo. Sirve si subiste una foto de la firma sobre una hoja.')
                ->action(fn () => $this->processSignature(notify: true)),
            DeleteAction::make()
                ->modalHeading(fn (): string => $this->record->is_legal_representative
                    ? 'Eliminar al representante legal'
                    : 'Eliminar empleado')
                ->modalDescription(fn (): string => $this->record->is_legal_representative
                    ? 'Este empleado es el representante legal. Si lo eliminas, los carnets quedan sin firma autorizada hasta que marques a otra persona.'
                    : 'Se eliminarán la ficha, las fotos y la firma de este empleado.'),
        ];
    }

    private function processSignature(bool $notify = true): void
    {
        try {
            app(EmployeeSignatureStore::class)->isolateInk($this->record);
            $this->refreshFormData(['authorized_signature']);
            if ($notify) {
                Notification::make()->success()->title('Fondo de la firma eliminado')->body('Quedó solo el trazo, listo para el carnet y los informes.')->send();
            }
        } catch (Throwable $exception) {
            Notification::make()->danger()->title('No fue posible limpiar la firma')->body($exception->getMessage())->send();
        }
    }

    private function processPhoto(): void
    {
        try {
            app(EmployeePhotoProcessor::class)->process($this->record);
            $this->refreshFormData(['photo_cutout']);
            Notification::make()->success()->title('Fondo eliminado')->body('Revisa el recorte y ajusta la posición antes de descargar el carnet.')->send();
        } catch (Throwable $exception) {
            Notification::make()->danger()->title('No fue posible procesar la fotografía')->body($exception->getMessage())->send();
        }
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Guardar cambios');
    }
}
