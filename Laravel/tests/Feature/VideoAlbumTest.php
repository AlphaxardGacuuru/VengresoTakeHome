<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VideoAlbum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VideoAlbumTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Index
     *
     * @return void
     */
    public function test_user_can_view_video_album_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();
        User::factory()->count(10)->create();

        VideoAlbum::factory()
            ->count(10)
            ->create(['username' => User::all()->random()->username]);

        $response = $this->get('api/video-albums');

        $response->assertStatus(200);
    }

    /**
     * Show
     *
     * @return void
     */
    public function test_user_can_view_one_video_album_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();

        $videoAlbum = VideoAlbum::factory()
            ->create(['username' => User::all()->random()->username]);

        $response = $this->get('api/video-albums/' . $videoAlbum->id);

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_create_video_album()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $cover = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post('api/video-albums', [
            'name' => 'Video Album 1',
            'released' => '2020-04-01',
            'cover' => $cover,
        ]);

        $response->assertStatus(200);

        Storage::assertExists('public/video-album-covers/' . $cover->hashName());

        Storage::delete('public/video-album-covers/' . $cover->hashName());
    }

    /**
     * Store Bad
     *
     * @return void
     */
    public function test_user_cannot_create_video_album_with_bad_data()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $response = $this->post('api/video-albums', []);

        $response->assertStatus(302);
    }

    /**
     * Update
     *
     * @return void
     */
    public function test_user_can_update_video_album()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $videoAlbum = VideoAlbum::factory()
            ->create(['username' => User::all()->random()->username]);

        $cover = UploadedFile::fake()->image('avatar.jpg');

        $this->put('api/video-albums/' . $videoAlbum->id, [
            'name' => 'Video Album One',
            'released' => '2020-04-01',
            'cover' => $cover,
        ]);

        // Get the new resource
        $this->get('api/video-albums/' . $videoAlbum->id)
            ->assertJsonFragment([
                'name' => 'Video Album One',
                'cover' => '/storage/video-album-covers/' . $cover->hashName(),
            ], $escape = true);

        Storage::assertExists('public/video-album-covers/' . $cover->hashName());

        // Delete Album Cover
        Storage::delete('public/video-album-covers/' . $cover->hashName());
    }
}
