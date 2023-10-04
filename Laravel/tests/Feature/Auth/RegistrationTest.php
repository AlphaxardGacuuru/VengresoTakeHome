<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register()
    {
		$this->markTestSkipped('must be revisited.');

        $response = $this->post('/register', [
            'name' => 'Black Music',
            'email' => 'al@black.co.ke',
            'avatar' => 'profile-pics/male_avatar.png',
            'username' => '@blackmusic',
            'phone' => '0700000000',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => "deviceName"
        ]);

        $this->assertAuthenticated();

        // $response->dump();
        // $response->assertNoContent();
    }
}
