<?php

namespace App\Services\Employees;

use App\Models\Employee;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class EmployeeVerificationQr
{
    public function dataUri(Employee $employee, int $size = 240): string
    {
        $builder = new Builder(
            writer: new PngWriter,
            writerOptions: [],
            validateResult: false,
            data: route('employees.verify', ['employee' => $employee->verification_token]),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 7,
            foregroundColor: new Color(12, 35, 50),
            backgroundColor: new Color(255, 255, 255),
        );

        return $builder->build()->getDataUri();
    }
}
