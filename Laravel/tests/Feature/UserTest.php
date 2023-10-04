<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use App\Notifications\FollowedNotification;
use Database\Seeders\UserSeeder;
use Database\Seeders\VideoAlbumSeeder;
use Database\Seeders\VideoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Update
     *
     * @return void
     */
    public function test_user_can_update_profile()
    {
        $user = User::factory()->black()->create();

        // Create Image
        $avatar = UploadedFile::fake()->image('avatar.jpg');

        $data = [
            "name" => "Black",
            'phone' => '0700123456',
            'bio' => 'Updated Bio',
            'withdrawal' => '500',
        ];

        $avatarUpload = $this->post('api/filepond/avatar/' . $user->id, ['filepond-avatar' => $avatar]);

        $avatarUpload->assertStatus(200);

        // Update Data
        $this->put('/api/users/' . $user->id, $data);

        // Get the new resource
        $this->get('api/users/' . $user->username)
            ->assertJsonFragment($data, $escape = true);

        Storage::assertExists('public/avatars/' . $avatar->hashName());

        // Delete Album Cover
        Storage::delete('public/avatars/' . $avatar->hashName());
    }

    /**
     * Update Bad
     *
     * @return void
     */
    public function test_user_cannot_update_profile_with_bad_data()
    {
        $user = User::factory()->black()->create();

        // Create Image
        $avatar = UploadedFile::fake()->image('avatar.mp3');

        $data = [
            "name" => "Black",
            'phone' => '070012345',
            'bio' => 'Updated Bio',
            'withdrawal' => '500',
        ];

        $avatarUpload = $this->post('api/filepond/avatar/' . $user->id, ['filepond-avatar' => $avatar]);

        $avatarUpload->assertStatus(302);

        // Update Data
        $response = $this->put('/api/users/' . $user->id, $data);

        $response->assertStatus(302);

        Storage::assertMissing('public/avatars/' . $avatar->hashName());

        // Delete Album Cover
        Storage::delete('public/avatars/' . $avatar->hashName());
    }

    /**
     * Follow
     *
     * @return void
     */
    public function test_user_can_follow_another_user()
    {
        $this->seed([
            UserSeeder::class,
            VideoAlbumSeeder::class,
            VideoSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::get()->first(),
            ['*']
        );

        Notification::fake();

        Mail::fake();

        $musician = Video::get()->first()->user;

        $response1 = $this->post("api/bought-videos");

        $response1->assertStatus(200);

        $response2 = $this->post("api/follows", [
            "musician" => $musician->username,
        ]);

        $response2->assertStatus(200);

        $this->assertDatabaseHas("follows", [
            "followed" => $musician->username,
            "username" => $user->username,
        ]);

        Notification::assertSentTo($musician, FollowedNotification::class);
    }

    /**
     * Can't Follow
     *
     * @return void
     */
    public function test_user_cannot_follow_another_user()
    {
        $this->seed([
            UserSeeder::class,
            VideoAlbumSeeder::class,
            VideoSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $musician = Video::where("username", "!=", "@blackmusic")
            ->get()
            ->random();

        $response = $this->post("api/follows", [
            "musician" => $musician->username,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing("follows", [
            "followed" => $musician->username,
            "username" => $user->username,
        ]);
    }

    /**
     * Can become musician
     *
     * @return void
     */
    public function test_user_can_become_musician()
    {
        Sanctum::actingAs(
            $user = User::factory()->black()->create(),
            ['*']
        );

        $response = $this->post("api/users/" . $user->id, [
            "accountType" => "musician",
            "_method" => "PUT",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "account_type" => "musician",
        ]);
    }
}
