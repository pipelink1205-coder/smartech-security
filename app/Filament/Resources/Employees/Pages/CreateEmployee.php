<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\Employees\Pages\Concerns\SavesDrawnSignature;
use App\Services\Employees\EmployeePhotoProcessor;
use App\Services\Employees\EmployeeSignatureStore;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Throwable;

class CreateEmployee extends CreateRecord
{
    use SavesDrawnSignature;

    protected static string $resource = EmployeeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->persistDrawnSignature($data);
    }

    protected function afterCreate(): void
    {
        $this->storeDrawnSignature();

        if (filled($this->record->authorized_signature)) {
            try {
                app(EmployeeSignatureStore::class)->isolateInk($this->record);
            } catch (Throwable $exception) {
                Notification::make()->warning()->title('Firma guardada con fondo')->body($exception->getMessage())->send();
            }
        }

        if (filled($this->record->photo_card) && blank($this->record->photo_cutout)) {
            try {
                app(EmployeePhotoProcessor::class)->process($this->record);
            } catch (Throwable $exception) {
                Notification::make()->warning()->title('Empleado creado sin recorte automático')->body($exception->getMessage())->send();
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return EmployeeResource::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Empleado guardado')
            ->body('Ajusta la foto en la vista previa y genera el PDF cuando quede bien.');
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->label('Guardar empleado');
    }
}
