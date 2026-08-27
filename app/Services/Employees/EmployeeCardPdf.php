<?php

namespace App\Services\Employees;

use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfDocument;

class EmployeeCardPdf
{
    public function make(Employee $employee): DomPdfDocument
    {
        return Pdf::loadView('pdf.employee-card', [
            'employee' => $employee,
            'card' => app(EmployeeCardViewData::class)->forEmployee($employee),
        ])
            ->setPaper([0, 0, 242.65, 153.07]);
    }
}
