<?php

namespace Tests\Unit\Services;

use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ExportService $ExportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ExportService = new ExportService;
    }

    #[Test]
    public function admin_cannot_export_reservations_from_other_admin_properties()
    {
        $adminA = User::factory()->create([
            'is_admin' => true,
        ]);

        $adminB = User::factory()->create([
            'is_admin' => true,
        ]);

        $propertyA = Property::factory()->create([
            'owner_id' => $adminA->id,
        ]);

        $propertyB = Property::factory()->create([
            'owner_id' => $adminB->id,
        ]);

        $reservationA = Reservation::factory()->create([
            'property_id' => $propertyA->id,
            'status' => 'confirmed',
        ]);

        $reservationB = Reservation::factory()->create([
            'property_id' => $propertyB->id,
            'status' => 'confirmed',
        ]);

        $service = app(ExportService::class);

        $result = $service->downloadInvoicesExcel(
            $adminA,
            [$reservationA->id, $reservationB->id],
            1.0,
        );

        $reservationA->refresh();
        $reservationB->refresh();

        $this->assertInstanceOf(
            BinaryFileResponse::class,
            $result,
        );

        $this->assertTrue($reservationA->invoice);
        $this->assertFalse($reservationB->invoice);
    }

    #[Test]
    public function admin_can_download_reservations_zip(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $property = Property::factory()->create([
            'owner_id' => $admin->id,
        ]);

        $guest = User::factory()->create([
            'is_admin' => false,
        ]);

        Reservation::factory()->create([
            'property_id' => $property->id,
            'user_id' => $guest->id,
            'status' => 'confirmed',
            'check_in' => now()->addDays(1),
            'check_out' => now()->addDays(4),
        ]);

        $result = $this->ExportService->downloadReservationsZip($admin);

        $this->assertInstanceOf(BinaryFileResponse::class, $result);
        $this->assertStringContainsString('attachment;', $result->headers->get('content-disposition'));
        $this->assertStringContainsString('.zip', $result->headers->get('content-disposition'));
    }

    #[Test]
    public function super_admin_can_export_facturas_from_any_admin_property(): void
    {
        config(['exports.super_admin_can_export_all' => true]);

        $superAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $adminA = User::factory()->create([
            'is_admin' => true,
        ]);

        $adminB = User::factory()->create([
            'is_admin' => true,
        ]);

        $propertyA = Property::factory()->create([
            'owner_id' => $adminA->id,
        ]);

        $propertyB = Property::factory()->create([
            'owner_id' => $adminB->id,
        ]);

        $reservationA = Reservation::factory()->create([
            'property_id' => $propertyA->id,
            'status' => 'confirmed',
        ]);

        $reservationB = Reservation::factory()->create([
            'property_id' => $propertyB->id,
            'status' => 'confirmed',
        ]);

        $result = $this->ExportService->downloadInvoicesExcel(
            $superAdmin,
            [$reservationA->id, $reservationB->id],
            1.0,
        );

        $reservationA->refresh();
        $reservationB->refresh();

        $this->assertInstanceOf(BinaryFileResponse::class, $result);
        $this->assertTrue($reservationA->invoice);
        $this->assertTrue($reservationB->invoice);
    }

    #[Test]
    public function super_admin_respects_config_when_restricted(): void
    {
        config(['exports.super_admin_can_export_all' => false]);

        $superAdmin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
        ]);

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $propertyA = Property::factory()->create([
            'owner_id' => $admin->id,
        ]);

        $reservationA = Reservation::factory()->create([
            'property_id' => $propertyA->id,
            'status' => 'confirmed',
        ]);

        $result = $this->ExportService->downloadInvoicesExcel(
            $superAdmin,
            [$reservationA->id],
            1.0,
        );

        $reservationA->refresh();

        $this->assertInstanceOf(BinaryFileResponse::class, $result);
        // Since config disables export-all, the super admin should NOT mark this reservation
        $this->assertFalse($reservationA->invoice);
    }

    #[Test]
    public function multiple_reservations_are_marked_as_invoiced(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $property = Property::factory()->create([
            'owner_id' => $admin->id,
        ]);

        $reservations = Reservation::factory()->count(3)->create([
            'property_id' => $property->id,
            'status' => 'confirmed',
        ]);

        $ids = $reservations->pluck('id')->toArray();

        $result = $this->ExportService->downloadInvoicesExcel(
            $admin,
            $ids,
            1.0,
        );

        $this->assertInstanceOf(BinaryFileResponse::class, $result);

        foreach ($reservations as $reservation) {
            $this->assertTrue($reservation->fresh()->invoice);
        }
    }
}
