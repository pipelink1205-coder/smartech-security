<?php

namespace Tests\Feature;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use App\Services\Employees\EmployeeCardPdf;
use App\Services\Employees\EmployeeCardViewData;
use App\Services\Employees\EmployeePhotoProcessor;
use App\Services\Employees\EmployeeSignatureStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class EmployeeCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_receives_public_code_and_document_is_encrypted(): void
    {
        $employee = Employee::create([
            'first_names' => 'Juan Felipe',
            'last_names' => 'Ortega Mejía',
            'document_number' => '1234567890',
            'position' => 'Técnico de proyectos',
        ]);

        $this->assertSame('STS-0001', $employee->employee_code);
        $this->assertNotEmpty($employee->verification_token);
        $this->assertGreaterThanOrEqual(40, strlen($employee->verification_token));
        $this->assertSame('1234567890', $employee->document_number);
        $this->assertNotSame('1234567890', $employee->getRawOriginal('document_number'));
    }

    public function test_photo_processor_removes_only_background_connected_to_edges(): void
    {
        Storage::fake('local');
        $source = imagecreatetruecolor(120, 160);
        $white = imagecolorallocate($source, 255, 255, 255);
        $navy = imagecolorallocate($source, 12, 28, 48);
        imagefill($source, 0, 0, $white);
        imagefilledellipse($source, 60, 55, 42, 52, $navy);
        imagefilledrectangle($source, 32, 78, 88, 159, $navy);
        ob_start();
        imagejpeg($source, null, 95);
        $bytes = ob_get_clean();
        imagedestroy($source);
        Storage::disk('local')->put('employees/originals/test.jpg', $bytes);

        $employee = Employee::create([
            'first_names' => 'Juan',
            'last_names' => 'Ortega',
            'position' => 'Técnico',
            'photo_card' => 'employees/originals/test.jpg',
        ]);

        $relative = app(EmployeePhotoProcessor::class)->process($employee);
        $result = imagecreatefrompng(Storage::disk('local')->path($relative));
        $cornerAlpha = (imagecolorat($result, 0, 0) >> 24) & 0x7F;
        $subjectAlpha = (imagecolorat($result, 60, 90) >> 24) & 0x7F;
        imagedestroy($result);

        $this->assertGreaterThan(20, $cornerAlpha);
        $this->assertLessThan(20, $subjectAlpha);
    }

    public function test_transparent_png_keeps_subject_and_does_not_eat_dark_clothing(): void
    {
        Storage::fake('local');
        $source = imagecreatetruecolor(120, 160);
        imagealphablending($source, false);
        imagesavealpha($source, true);
        $clear = imagecolorallocatealpha($source, 0, 0, 0, 127);
        $navy = imagecolorallocatealpha($source, 12, 28, 48, 0);
        imagefilledrectangle($source, 0, 0, 119, 159, $clear);
        imagefilledellipse($source, 60, 55, 42, 52, $navy);
        imagefilledrectangle($source, 32, 78, 88, 159, $navy);
        ob_start();
        imagepng($source);
        $bytes = ob_get_clean();
        imagedestroy($source);
        Storage::disk('local')->put('employees/card-sources/cut.png', $bytes);

        $employee = Employee::create([
            'first_names' => 'Juan',
            'last_names' => 'Ortega',
            'position' => 'Técnico',
            'photo_card' => 'employees/card-sources/cut.png',
        ]);

        $relative = app(EmployeePhotoProcessor::class)->process($employee);
        $result = imagecreatefrompng(Storage::disk('local')->path($relative));
        $cornerAlpha = (imagecolorat($result, 2, 2) >> 24) & 0x7F;
        $subjectAlpha = (imagecolorat($result, 60, 90) >> 24) & 0x7F;
        imagedestroy($result);

        $this->assertGreaterThan(80, $cornerAlpha);
        $this->assertLessThan(20, $subjectAlpha);
    }

    public function test_checkerboard_pixels_become_transparent(): void
    {
        Storage::fake('local');
        $source = imagecreatetruecolor(80, 80);
        $white = imagecolorallocate($source, 255, 255, 255);
        $gray = imagecolorallocate($source, 204, 204, 204);
        $navy = imagecolorallocate($source, 12, 28, 48);
        for ($y = 0; $y < 80; $y++) {
            for ($x = 0; $x < 80; $x++) {
                imagesetpixel($source, $x, $y, ((int) floor($x / 8) + (int) floor($y / 8)) % 2 === 0 ? $white : $gray);
            }
        }
        imagefilledellipse($source, 40, 40, 28, 36, $navy);
        ob_start();
        imagepng($source);
        $bytes = ob_get_clean();
        imagedestroy($source);
        Storage::disk('local')->put('employees/card-sources/board.png', $bytes);

        $employee = Employee::create([
            'first_names' => 'Ana',
            'last_names' => 'Restrepo',
            'position' => 'Técnica',
            'photo_card' => 'employees/card-sources/board.png',
        ]);

        $relative = app(EmployeePhotoProcessor::class)->process($employee);
        $result = imagecreatefrompng(Storage::disk('local')->path($relative));
        $cornerAlpha = (imagecolorat($result, 1, 1) >> 24) & 0x7F;
        $subjectAlpha = (imagecolorat($result, 40, 40) >> 24) & 0x7F;
        imagedestroy($result);

        $this->assertGreaterThan(80, $cornerAlpha);
        $this->assertLessThan(20, $subjectAlpha);
    }

    public function test_original_photo_is_never_used_as_the_card_source(): void
    {
        $employee = Employee::create([
            'first_names' => 'Juan',
            'last_names' => 'Ortega',
            'position' => 'Técnico',
            'photo_original' => 'employees/originals/private.jpg',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('fotografía para el carnet');

        app(EmployeePhotoProcessor::class)->process($employee);
    }

    public function test_card_pdf_is_generated_with_verification_qr_without_document_number(): void
    {
        $employee = Employee::create([
            'first_names' => 'Juan Felipe',
            'last_names' => 'Ortega Mejía',
            'document_number' => '1234567890',
            'position' => 'Técnico de proyectos',
        ]);

        $pdf = app(EmployeeCardPdf::class)->make($employee)->output();

        $this->assertStringStartsWith('%PDF-', $pdf);
        $this->assertGreaterThan(10000, strlen($pdf));
        $this->assertStringNotContainsString('1234567890', $pdf);
    }

    public function test_public_verification_shows_only_professional_information(): void
    {
        $employee = Employee::create([
            'first_names' => 'Juan Felipe',
            'last_names' => 'Ortega Mejía',
            'document_number' => '1234567890',
            'position' => 'Ingeniero de sistemas',
        ]);

        $this->get(route('employees.verify', ['employee' => $employee->verification_token]))
            ->assertOk()
            ->assertSee('JUAN FELIPE ORTEGA MEJÍA')
            ->assertSee('Ingeniero de sistemas')
            ->assertSee('EMPLEADO VERIFICADO')
            ->assertSee('NIT 901.124.137-1')
            ->assertSee('Contactar a Smart Tech Security')
            ->assertDontSee('1234567890')
            ->assertDontSee('Credencial activa');
    }

    public function test_admin_can_open_employee_module_preview_and_pdf(): void
    {
        $user = User::factory()->create();
        $employee = Employee::create([
            'first_names' => 'Juan Felipe',
            'last_names' => 'Ortega Mejía',
            'position' => 'Técnico de proyectos',
        ]);

        $this->actingAs($user)->get(EmployeeResource::getUrl('index'))->assertOk();
        $this->actingAs($user)->get(EmployeeResource::getUrl('create'))->assertOk();
        $this->actingAs($user)->get(EmployeeResource::getUrl('edit', ['record' => $employee]))->assertOk();
        $this->actingAs($user)
            ->get(route('admin.employees.card-preview', $employee))
            ->assertOk()
            ->assertSee('employee-card__foreground', escape: false);
        $this->actingAs($user)
            ->get(route('admin.employees.card-pdf', $employee))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->actingAs($user)
            ->get(route('admin.employees.photo', $employee))
            ->assertNotFound();
    }

    public function test_employee_photo_is_only_visible_to_admins(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('employees/originals/face.jpg', 'PUBLIC-FACE');

        $employee = Employee::create([
            'first_names' => 'Juan',
            'last_names' => 'Ortega',
            'position' => 'Técnico',
            'photo_original' => 'employees/originals/face.jpg',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.employees.photo', $employee))
            ->assertOk()
            ->assertSee('PUBLIC-FACE');
    }

    public function test_only_one_legal_representative_stays_active(): void
    {
        $first = Employee::create([
            'first_names' => 'Ana',
            'last_names' => 'Restrepo',
            'position' => 'Gerente',
            'is_legal_representative' => true,
        ]);
        $second = Employee::create([
            'first_names' => 'Luis',
            'last_names' => 'Gómez',
            'position' => 'Representante legal',
            'is_legal_representative' => true,
        ]);

        $this->assertFalse($first->fresh()->is_legal_representative);
        $this->assertTrue($second->fresh()->is_legal_representative);
        $this->assertTrue($second->is(Employee::legalRepresentative()));
    }

    public function test_cards_use_the_legal_representative_signature(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('employees/signatures/rep.png', 'SIGNATURE-BYTES');

        Employee::create([
            'first_names' => 'Ana',
            'last_names' => 'Restrepo',
            'position' => 'Representante legal',
            'is_legal_representative' => true,
            'authorized_signature' => 'employees/signatures/rep.png',
        ]);

        $technician = Employee::create([
            'first_names' => 'Juan',
            'last_names' => 'Ortega',
            'position' => 'Técnico',
        ]);

        $card = app(EmployeeCardViewData::class)->forEmployee($technician);

        $this->assertNotEmpty($card['signature']);
        $this->assertStringContainsString('base64,', $card['signature']);
    }

    public function test_own_signature_does_not_print_unless_legal_representative(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('employees/signatures/juan.png', 'JUAN-SIGNATURE');

        $employee = Employee::create([
            'first_names' => 'Juan',
            'last_names' => 'Ortega',
            'position' => 'Técnico',
            'authorized_signature' => 'employees/signatures/juan.png',
        ]);

        $card = app(EmployeeCardViewData::class)->forEmployee($employee);

        $this->assertNull($card['signature']);
    }

    public function test_public_verification_prefers_the_original_photo(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('employees/originals/face.jpg', 'PUBLIC-FACE');
        Storage::disk('local')->put('employees/cutouts/cut.png', 'CUTOUT');

        $employee = Employee::create([
            'first_names' => 'Juan',
            'last_names' => 'Ortega',
            'position' => 'Técnico',
            'photo_original' => 'employees/originals/face.jpg',
            'photo_cutout' => 'employees/cutouts/cut.png',
        ]);

        $decoded = base64_decode((string) substr((string) $employee->public_photo_data_uri, strpos((string) $employee->public_photo_data_uri, ',') + 1), true);

        $this->assertSame('PUBLIC-FACE', $decoded);
        $this->assertNotSame('CUTOUT', $decoded);
    }

    public function test_uploaded_signature_keeps_ink_and_drops_paper_background(): void
    {
        Storage::fake('local');
        $source = imagecreatetruecolor(200, 80);
        $white = imagecolorallocate($source, 255, 255, 255);
        $navy = imagecolorallocate($source, 12, 28, 48);
        imagefill($source, 0, 0, $white);
        imagefilledrectangle($source, 20, 30, 160, 42, $navy);
        ob_start();
        imagejpeg($source, null, 95);
        $bytes = ob_get_clean();
        imagedestroy($source);
        Storage::disk('local')->put('employees/signatures/paper.jpg', $bytes);

        $employee = Employee::create([
            'first_names' => 'Juan',
            'last_names' => 'Ortega',
            'position' => 'Técnico',
            'authorized_signature' => 'employees/signatures/paper.jpg',
        ]);

        $relative = app(EmployeeSignatureStore::class)->isolateInk($employee);
        $result = imagecreatefrompng(Storage::disk('local')->path($relative));
        $width = imagesx($result);
        $height = imagesy($result);
        $cornerAlpha = (imagecolorat($result, 1, 1) >> 24) & 0x7F;
        $inkAlpha = (imagecolorat($result, (int) ($width / 2), (int) ($height / 2)) >> 24) & 0x7F;
        imagedestroy($result);

        $this->assertGreaterThan(80, $cornerAlpha);
        $this->assertLessThan(40, $inkAlpha);
        $this->assertSame($relative, $employee->fresh()->authorized_signature);
    }
}
