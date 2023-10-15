<?php

namespace Tests\Feature;

use App\Models\DivCount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DivCountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Index
     *
     * @return void
     */
    public function test_user_can_view_div_count_resource()
    {
        DivCount::factory()->count(10)->create();

        $response = $this->get('api/div-counts');

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_create_div_count()
    {
        $response = $this->post('api/div-counts', [
            'url' => 'https://example.com',
            'count' => 120,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_cannot_create_div_count()
    {
        $response = $this->post('api/div-counts', [
            'url' => 'https://example.com',
        ]);

        $response->assertStatus(302);
    }
}
