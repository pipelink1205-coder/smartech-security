<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\Employees\EmployeeCardPdf;
use App\Services\Employees\EmployeeCardViewData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class EmployeeCardController extends Controller
{
    public function preview(Employee $employee, EmployeeCardViewData $cards): View
    {
        return view('employees.card-preview', [
            'employee' => $employee,
            'card' => $cards->forEmployee($employee),
        ]);
    }

    public function pdf(Employee $employee, EmployeeCardPdf $cards): Response
    {
        abort_unless($employee->status === 'active', 422, 'Solo se puede generar el carnet de un empleado activo.');

        return $cards->make($employee)->download('carnet-'.strtolower($employee->employee_code).'.pdf');
    }

    public function verify(Employee $employee): View
    {
        return view('employees.verify', compact('employee'));
    }

    public function photo(Employee $employee): Response
    {
        $path = collect([
            $employee->photo_original,
            $employee->photo_card,
            $employee->photo_cutout,
        ])->first(fn (?string $path): bool => filled($path) && Storage::disk('local')->exists($path));

        abort_unless(is_string($path), 404);

        return response(Storage::disk('local')->get($path), 200, [
            'Content-Type' => Storage::disk('local')->mimeType($path) ?: 'image/jpeg',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
