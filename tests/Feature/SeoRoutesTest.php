<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SeoRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function sitemap_lists_the_homepage_and_every_property(): void
    {
        $properties = Property::factory()->count(2)->create();

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/xml');

        foreach ($properties as $property) {
            $response->assertSee(route('property.show', $property->id), false);
        }
    }

    #[Test]
    public function robots_txt_references_the_sitemap(): void
    {
        $response = $this->get(route('robots'));

        $response->assertOk();
        $response->assertSee('Sitemap: ' . url('/sitemap.xml'), false);
    }
}
