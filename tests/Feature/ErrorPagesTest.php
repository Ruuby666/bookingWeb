<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_nonexistent_property_shows_the_branded_404_page(): void
    {
        // assertViewIs() isn't reliable here: exception-rendered responses
        // (vs. a controller returning a view directly) don't populate the
        // response the same way — verified with a real request instead
        // (see AUDITORIA-PRELANZAMIENTO.md, F28) that this exact content
        // really renders for a live 404.
        $this->get('/property/999999')
            ->assertStatus(404)
            ->assertSee('404')
            ->assertSee('Page Not Found');
    }

    /**
     * CSRF verification is bypassed while running tests (Laravel's
     * VerifyCsrfToken::runningUnitTests()), so a real 419 can't be
     * triggered here — confirmed with a live request instead (see
     * AUDITORIA-PRELANZAMIENTO.md, F28). This asserts the view itself
     * renders correctly, which is what's actually testable in-process.
     */
    #[Test]
    public function the_419_view_renders_with_the_expected_content(): void
    {
        $html = view('errors.419')->render();

        $this->assertStringContainsString('419', $html);
        $this->assertStringContainsString('Session Expired', $html);
    }
}
