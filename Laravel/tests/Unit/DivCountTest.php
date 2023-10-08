<?php

namespace Tests\Unit;

use App\Models\DivCount;
use PHPUnit\Framework\TestCase;

class DivCountTest extends TestCase
{

    /**
     * Index
     *
     * @return void
     */
    public function test_user_can_view_post_resource()
    {
        // Create Users with @blackmusic first
        DivCount::factory()->count(10)->create();

        $response = $this->get('api/div-counts');

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_create_post()
    {
        $response = $this->post('api/posts', [
            'url' => 'https://example.com',
            'count' => 120,
        ]);

        $response->assertStatus(200);
    }
}
