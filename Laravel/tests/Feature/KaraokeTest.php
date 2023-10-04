<?php

namespace Tests\Feature;

use App\Models\Audio;
use App\Models\Karaoke;
use App\Models\KaraokeComment;
use App\Models\User;
use Database\Seeders\AudioAlbumSeeder;
use Database\Seeders\AudioSeeder;
use Database\Seeders\KaraokeSeeder;
use Illuminate\Http\UploadedFile;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class KaraokeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Index
     *
     * @return void
     */
    public function test_user_can_view_karaoke_resource()
    {
        $this->seed([
            UserSeeder::class,
            AudioAlbumSeeder::class,
            AudioSeeder::class,
            KaraokeSeeder::class,
        ]);

        $response = $this->get('api/karaokes');

        $response->assertStatus(200);
    }

    /**
     * Show
     *
     * @return void
     */
    public function test_user_can_view_one_karaoke_resource()
    {
        $this->seed([
            UserSeeder::class,
            AudioAlbumSeeder::class,
            AudioSeeder::class,
            KaraokeSeeder::class,
        ]);

        $karaoke = Karaoke::all()->random();

        $response = $this->get('api/karaokes/' . $karaoke->id);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            "id" => $karaoke->id,
            "karaoke" => $karaoke->karaoke,
            "description" => $karaoke->description,
        ]);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_create_karaoke()
    {
        $this->seed([
            UserSeeder::class,
            AudioAlbumSeeder::class,
            AudioSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $audio = Audio::all()->random();

        $karaoke = UploadedFile::fake()->create('karaoke.mp4');

        // Upload karaoke
        $this->post('api/filepond/karaokes', ['filepond-karaoke' => $karaoke]);

        $response = $this->post('api/karaokes', [
            "karaoke" => 'karaokes/' . $karaoke->hashName(),
            "audio_id" => $audio->id,
            "description" => "Some description",
        ]);

        $response->assertStatus(200);

        Storage::assertExists('public/karaokes/' . $karaoke->hashName());

        Storage::delete('public/karaokes/' . $karaoke->hashName());
    }

    /**
     * Store Bad
     *
     * @return void
     */
    public function test_user_cannot_create_karaoke_with_bad_data()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $response = $this->post('api/karaokes', []);

        $response->assertStatus(302);
    }

    /**
     * Karaoke Like
     *
     * @return void
     */
    public function test_user_can_like_karaoke()
    {
        $this->seed([
            UserSeeder::class,
            AudioAlbumSeeder::class,
            AudioSeeder::class,
            KaraokeSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $karaoke = Karaoke::all()->random();

        // Store
        $response = $this->post('api/karaoke-likes', [
            'karaoke' => $karaoke->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("karaoke_likes", [
            "karaoke_id" => $karaoke->id,
            "username" => $user->username,
        ]);
    }

    /**
     * Karaoke Comment
     *
     * @return void
     */
    public function test_user_can_comment_on_karaoke()
    {
        $this->seed([
            UserSeeder::class,
            AudioAlbumSeeder::class,
            AudioSeeder::class,
            KaraokeSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $karaoke = Karaoke::all()->random();

        // Store
        $response = $this->post('api/karaoke-comments', [
            'id' => $karaoke->id,
            "text" => "Some text",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("karaoke_comments", [
            "karaoke_id" => $karaoke->id,
            "text" => "Some text",
            "username" => $user->username,
        ]);
    }

    /**
     * Karaoke Comment Like
     *
     * @return void
     */
    public function test_user_can_like_karaoke_comment()
    {
        $this->seed([
            UserSeeder::class,
            AudioAlbumSeeder::class,
            AudioSeeder::class,
            KaraokeSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $comment = KaraokeComment::all()->random();

        // Store
        $response = $this->post('api/karaoke-comment-likes', [
            "comment" => $comment->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("karaoke_comment_likes", [
            "karaoke_comment_id" => $comment->id,
            "username" => $user->username,
        ]);
    }
}
