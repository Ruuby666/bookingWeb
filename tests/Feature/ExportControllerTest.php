<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class ExportControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
        ]);
    }

    private function regularUser(): User
    {
        return User::factory()->create([
            'is_admin' => false,
        ]);
    }

    #[Test]
    public function guest_cannot_export_reservations(): void
    {
        $this->get(route('admin.calendar.export-excel'))
            ->assertRedirect('/');
    }

    #[Test]
    public function non_admin_cannot_export_reservations(): void
    {
        $this->actingAs($this->regularUser())
            ->get(route('admin.calendar.export-excel'))
            ->assertRedirect('/');
    }

    #[Test]
    public function admin_calls_export_service_to_download_reservations(): void
    {
        $mock = Mockery::mock(ExportService::class);

        $mock->shouldReceive('downloadReservationsZip')
            ->once()
            ->with(Mockery::type(User::class))
            ->andReturn(new BinaryFileResponse(__FILE__));

        $this->app->instance(ExportService::class, $mock);

        $this->actingAs($this->adminUser())
            ->get(route('admin.calendar.export-excel'))
            ->assertOk();
    }

    #[Test]
    public function guest_cannot_export_invoices(): void
    {
        $this->post(route('admin.calendar.export-invoice-excel'))
            ->assertRedirect('/');
    }

    #[Test]
    public function non_admin_cannot_export_invoices(): void
    {
        $this->actingAs($this->regularUser())
            ->post(route('admin.calendar.export-invoice-excel'))
            ->assertRedirect('/');
    }

    #[Test]
    public function invoice_export_requires_valid_data(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('admin.calendar.export-invoice-excel'), [])
            ->assertSessionHasErrors();
    }

    #[Test]
    public function admin_calls_export_service_to_download_invoices(): void
    {
        $mock = Mockery::mock(ExportService::class);

        $mock->shouldReceive('downloadInvoicesExcel')
            ->once()
            ->with(
                Mockery::type(User::class),
                [1, 2],
                150.5,
            )
            ->andReturn(new BinaryFileResponse(__FILE__));

        $this->app->instance(ExportService::class, $mock);

        $this->actingAs($this->adminUser())
            ->post(route('admin.calendar.export-invoice-excel'), [
                'ids' => [1, 2],
                'invoice_amount' => 150.5,
            ])
            ->assertOk();
    }
}
