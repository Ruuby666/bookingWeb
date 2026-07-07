<?php

namespace Tests\Unit\Services;

use App\Models\Property;
use App\Models\User;
use App\Services\BookingDateService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingDateServiceTest extends TestCase
{
    private BookingDateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BookingDateService;
    }

    private function makeProperty(string $title): Property
    {
        return Property::factory()->make(['title' => $title, 'owner_id' => 1]);
    }

    #[Test]
    public function it_uses_the_apartment_check_in_hour_by_default(): void
    {
        $property = $this->makeProperty('Sunny Flat');

        $dates = $this->service->parse($property, '01/07/2026 - 05/07/2026');

        $this->assertSame('01/07/2026 14:00', $dates['checkIn']->format('d/m/Y H:i'));
        $this->assertSame('05/07/2026 11:00', $dates['checkOut']->format('d/m/Y H:i'));
    }

    #[Test]
    public function it_uses_the_villa_check_in_hour_when_title_contains_villa(): void
    {
        $property = $this->makeProperty('Villa Marina');

        $dates = $this->service->parse($property, '01/07/2026 - 05/07/2026');

        $this->assertSame('01/07/2026 15:00', $dates['checkIn']->format('d/m/Y H:i'));
    }

    #[Test]
    public function it_uses_the_villa_check_in_hour_when_title_contains_casa(): void
    {
        $property = $this->makeProperty('Casa del Sol');

        $dates = $this->service->parse($property, '01/07/2026 - 05/07/2026');

        $this->assertSame('01/07/2026 15:00', $dates['checkIn']->format('d/m/Y H:i'));
    }

    #[Test]
    public function it_throws_when_daterange_has_no_separator(): void
    {
        $property = $this->makeProperty('Sunny Flat');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date format.');

        $this->service->parse($property, '01/07/2026');
    }

    #[Test]
    public function it_throws_when_dates_are_not_parseable(): void
    {
        $property = $this->makeProperty('Sunny Flat');

        $this->expectException(InvalidArgumentException::class);

        $this->service->parse($property, 'not-a-date - also-not-a-date');
    }
}
