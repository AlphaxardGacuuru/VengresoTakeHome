<?php

namespace Tests\Feature;

use App\Models\AudioAlbum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AudioAlbumTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Index
     *
     * @return void
     */
    public function test_user_can_view_audio_album_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();
        User::factory()->count(10)->create();

        AudioAlbum::factory()
            ->count(10)
            ->create(['username' => User::all()->random()->username]);

        $response = $this->get('api/audio-albums');

        $response->assertStatus(200);
    }

    /**
     * Show
     *
     * @return void
     */
    public function test_user_can_view_one_audio_album_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();

        $audioAlbum = AudioAlbum::factory()
            ->create(['username' => User::all()->random()->username]);

        $response = $this->get('api/audio-albums/' . $audioAlbum->id);

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_create_audio_album()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $cover = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post('api/audio-albums', [
            'name' => 'Audio Album 1',
            'released' => '2020-04-01',
            'cover' => $cover,
        ]);

        $response->assertStatus(200);

        Storage::assertExists('public/audio-album-covers/' . $cover->hashName());

        Storage::delete('public/audio-album-covers/' . $cover->hashName());
    }

    /**
     * Store Bad
     *
     * @return void
     */
    public function test_user_cannot_create_audio_album_with_bad_data()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $response = $this->post('api/audio-albums', []);

        $response->assertStatus(302);
    }

    /**
     * Update
     *
     * @return void
     */
    public function test_user_can_update_audio_album()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $audioAlbum = AudioAlbum::factory()
            ->create(['username' => User::all()->random()->username]);

        $cover = UploadedFile::fake()->image('avatar.jpg');

        $this->put('api/audio-albums/' . $audioAlbum->id, [
            'name' => 'Audio Album One',
            'released' => '2020-04-01',
            'cover' => $cover,
        ]);

        // Get the new resource
        $this->get('api/audio-albums/' . $audioAlbum->id)
            ->assertJsonFragment([
                'name' => 'Audio Album One',
                'cover' => '/storage/audio-album-covers/' . $cover->hashName(),
            ], $escape = true);

        Storage::assertExists('public/audio-album-covers/' . $cover->hashName());

        // Delete Album Cover
        Storage::delete('public/audio-album-covers/' . $cover->hashName());
    }
}
