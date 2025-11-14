<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Ensure APP key is set at runtime for tests
        config(['app.key' => 'base64:4j4pfx9xPr25lmymVVKzlOP0m5G84HYuP1/K8bg7XA0=']);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
