<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test health check endpoint.
     */
    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/up');
        $response->assertStatus(200);
    }

    /**
     * Test sleek desktop landing page renders for guests.
     */
    public function test_desktop_landing_page_renders_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertSee('Global Virtual Number Verification Platform');
    }
}
