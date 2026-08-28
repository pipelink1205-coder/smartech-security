<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Services\Employees\EmployeeSignatureStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class EmployeeSeeder extends Seeder
{
    /** @var array<int, array<string, mixed>> */
    private array $employees = [
        [
            'employee_code' => 'STS-002-98714142',
            'first_names' => 'Juan Felipe',
            'last_names' => 'Ortega Mejía',
            'document_type' => 'CC',
            'document_number' => '98714142',
            'position' => 'Ingeniero de sistemas',
            'status' => 'active',
            'is_legal_representative' => false,
            'assets' => [
                'photo_original' => [
                    'source' => 'juan-felipe-ortega-mejia-public.png',
                    'destination' => 'employees/originals/seed-juan-felipe-ortega-mejia.png',
                ],
                'photo_card' => [
                    'source' => 'juan-felipe-ortega-mejia-card.png',
                    'destination' => 'employees/card-sources/seed-juan-felipe-ortega-mejia.png',
                ],
                'photo_cutout' => [
                    'source' => 'juan-felipe-ortega-mejia-cutout.png',
                    'destination' => 'employees/cutouts/seed-juan-felipe-ortega-mejia.png',
                ],
            ],
        ],
        [
            'employee_code' => 'STS-003-98687613',
            'first_names' => 'Juan',
            'last_names' => 'Jaramillo Pulgarín',
            'document_type' => 'CC',
            'document_number' => '98687613',
            'position' => 'Analista de soporte',
            'status' => 'active',
            'is_legal_representative' => false,
            'assets' => [
                'photo_original' => [
                    'source' => 'juan-jaramillo-pulgarin-public.jpg',
                    'destination' => 'employees/originals/seed-juan-jaramillo-pulgarin.jpg',
                ],
            ],
        ],
        [
            'employee_code' => 'STS-001-98663183',
            'first_names' => 'Andrés Felipe',
            'last_names' => 'Sierra',
            'document_type' => 'CC',
            'document_number' => '98663183',
            'position' => 'Gerente comercial',
            'status' => 'active',
            'is_legal_representative' => true,
            'assets' => [
                'photo_original' => [
                    'source' => 'andres-felipe-sierra-public.png',
                    'destination' => 'employees/originals/seed-andres-felipe-sierra.png',
                ],
            ],
            'signature' => 'andres-felipe-sierra-signature-source.jpg',
        ],
    ];

    public function run(): void
    {
        $seeded = [];

        foreach ($this->employees as $attributes) {
            $assets = $attributes['assets'];
            $signature = $attributes['signature'] ?? null;
            unset($attributes['assets'], $attributes['signature']);

            $employee = Employee::query()
                ->where('employee_code', $attributes['employee_code'])
                ->orWhere(function ($query) use ($attributes): void {
                    $query
                        ->where('first_names', $attributes['first_names'])
                        ->where('last_names', $attributes['last_names']);
                })
                ->first() ?? new Employee;

            $employee->fill($attributes);

            foreach ($assets as $field => $asset) {
                if (blank($employee->{$field})) {
                    $this->copyAsset($asset['source'], $asset['destination']);
                    $employee->{$field} = $asset['destination'];
                }
            }

            $employee->save();

            if ($signature && blank($employee->authorized_signature)) {
                $this->seedSignature($employee, $signature);
            }

            $seeded[] = $employee->fresh();
        }

        if ($this->command) {
            $this->command->newLine();
            $this->command->info('Empleados creados o actualizados. URLs públicas de verificación:');

            foreach ($seeded as $employee) {
                $this->command->line(sprintf(
                    '%s: %s',
                    $employee->full_name,
                    route('employees.verify', ['employee' => $employee->verification_token]),
                ));
            }
        }
    }

    private function copyAsset(string $sourceName, string $destination): void
    {
        $source = $this->assetPath($sourceName);

        if (! is_readable($source)) {
            throw new RuntimeException("No se encontró el recurso del empleado: {$sourceName}");
        }

        Storage::disk('local')->put($destination, file_get_contents($source));
    }

    private function seedSignature(Employee $employee, string $sourceName): void
    {
        $source = $this->assetPath($sourceName);
        $image = @imagecreatefromjpeg($source);

        if (! $image) {
            throw new RuntimeException('No fue posible leer la firma autorizada de Andrés Felipe Sierra.');
        }

        if (imagesy($image) > imagesx($image)) {
            $white = imagecolorallocate($image, 255, 255, 255);
            $rotated = imagerotate($image, 90, $white);
            imagedestroy($image);
            $image = $rotated;
        }

        ob_start();
        imagejpeg($image, null, 96);
        $bytes = ob_get_clean();
        imagedestroy($image);

        if (! is_string($bytes)) {
            throw new RuntimeException('No fue posible preparar la firma autorizada.');
        }

        $relative = 'employees/signatures/seed-andres-felipe-sierra.jpg';
        Storage::disk('local')->put($relative, $bytes);
        $employee->forceFill(['authorized_signature' => $relative])->saveQuietly();

        app(EmployeeSignatureStore::class)->isolateInk($employee);
    }

    private function assetPath(string $filename): string
    {
        return database_path('seeders/assets/employees/'.$filename);
    }
}
