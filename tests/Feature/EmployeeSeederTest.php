<?php

namespace Tests\Feature;

use App\Models\Employee;
use Database\Seeders\EmployeeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_the_authorized_employee_roster_idempotently(): void
    {
        Storage::fake('local');

        $this->seed(EmployeeSeeder::class);

        $this->assertDatabaseCount('employees', 3);
        $this->assertSame([
            'STS-001-98663183',
            'STS-002-98714142',
            'STS-003-98687613',
        ], Employee::query()->orderBy('employee_code')->pluck('employee_code')->all());

        $representative = Employee::query()->where('employee_code', 'STS-001-98663183')->firstOrFail();
        $juanFelipe = Employee::query()->where('employee_code', 'STS-002-98714142')->firstOrFail();
        $juanJaramillo = Employee::query()->where('employee_code', 'STS-003-98687613')->firstOrFail();

        $this->assertTrue($representative->is_legal_representative);
        $this->assertSame('98663183', $representative->document_number);
        $this->assertNotSame('98663183', $representative->getRawOriginal('document_number'));
        $this->assertNotEmpty($representative->authorized_signature);
        $this->assertNotEmpty($representative->verification_token);
        $this->assertSame('98714142', $juanFelipe->document_number);
        $this->assertSame('98687613', $juanJaramillo->document_number);

        Storage::disk('local')->assertExists($representative->photo_original);
        Storage::disk('local')->assertExists($representative->authorized_signature);
        Storage::disk('local')->assertExists($juanFelipe->photo_original);
        Storage::disk('local')->assertExists($juanFelipe->photo_card);
        Storage::disk('local')->assertExists($juanFelipe->photo_cutout);
        Storage::disk('local')->assertExists($juanJaramillo->photo_original);

        $tokens = Employee::query()->pluck('verification_token', 'employee_code')->all();
        $this->seed(EmployeeSeeder::class);

        $this->assertDatabaseCount('employees', 3);
        $this->assertSame($tokens, Employee::query()->pluck('verification_token', 'employee_code')->all());
    }
}
