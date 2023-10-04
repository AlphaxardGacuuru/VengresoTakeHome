<?php

namespace Tests\Feature;

use App\Models\Audio;
use App\Models\AudioAlbum;
use App\Models\AudioComment;
use App\Models\User;
use App\Notifications\AudioReceiptNotification;
use Database\Seeders\AudioAlbumSeeder;
use Database\Seeders\AudioSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AudioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Index
     *
     * @return void
     */
    public function test_user_can_view_audio_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();
        User::factory()->count(10)->create();

        $username = User::all()->random()->username;

        // Create Audio Album
        AudioAlbum::factory()->create(['username' => $username]);

        $album = AudioAlbum::first()->id;

        Audio::factory()
            ->count(10)
            ->create([
                'username' => $username,
                'audio_album_id' => $album,
            ]);

        $response = $this->get('api/audios');

        $response->assertStatus(200);
    }

    /**
     * Show
     *
     * @return void
     */
    public function test_user_can_view_one_audio_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();
        User::factory()->count(10)->create();

        $username = User::all()->random()->username;

        // Create Audio Album
        AudioAlbum::factory()->create(['username' => $username]);

        $album = AudioAlbum::first()->id;

        Audio::factory()
            ->create([
                'username' => $username,
                'audio_album_id' => $album,
            ]);

        $audio = Audio::first()->id;

        $response = $this->get('api/audios/' . $audio);

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_create_audio()
    {
        Sanctum::actingAs(
            $user = User::factory()->black()->create(),
            ['*']
        );

        $user2 = User::factory()->create();

        $album = AudioAlbum::factory()->create(['username' => $user->username]);

        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');

        $audio = UploadedFile::fake()->create('audio.mp3');

        // Upload audio and thumbnail
        $uploadThumbnail = $this->post('api/filepond/audio-thumbnail', ['filepond-thumbnail' => $thumbnail]);
        $uploadAudio = $this->post('api/filepond/audio', ['filepond-audio' => $audio]);

        $uploadThumbnail->assertStatus(200);
        $uploadAudio->assertStatus(200);

        $response = $this->post('api/audios', [
            'audio' => 'audios/' . $audio->hashName(),
            'thumbnail' => 'audio-thumbnails/' . $thumbnail->hashName(),
            'name' => 'Audio 1',
            'ft' => $user2->username,
            'audio_album_id' => $album->id,
            'genre' => 'Country',
            'released' => '2020-04-01',
            'description' => 'This is video',
        ]);

        $response->assertStatus(200);

        Storage::assertExists('public/audio-thumbnails/' . $thumbnail->hashName());
        Storage::assertExists('public/audios/' . $audio->hashName());

        Storage::delete('public/audio-thumbnails/' . $thumbnail->hashName());
        Storage::delete('public/audios/' . $audio->hashName());
    }

    /**
     * Store Bad
     *
     * @return void
     */
    public function test_user_cannot_create_audio_with_bad_data()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $response = $this->post('api/audios', []);

        $response->assertStatus(302);
    }

    /**
     * Update
     *
     * @return void
     */
    public function test_user_can_update_audio()
    {
        Sanctum::actingAs(
            $user = User::factory()->black()->create(),
            ['*']
        );

        // Create Audio Album
        $album = AudioAlbum::factory()->create(['username' => $user->username]);

        $audio = Audio::factory()
            ->create([
                'username' => $user->username,
                'audio_album_id' => $album,
            ]);

        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');

        $audioFile = UploadedFile::fake()->create('audio.mp3');

        // Upload audio and thumbnail
        $uploadThumbnail = $this->post('api/filepond/audio-thumbnail', ['filepond-thumbnail' => $thumbnail]);
        $uploadVideo = $this->post('api/filepond/audio', ['filepond-audio' => $audioFile]);

        $uploadThumbnail->assertStatus(200);
        $uploadVideo->assertStatus(200);

        $this->put('api/audios/' . $audio->id, [
            'audio' => 'audios/' . $audioFile->hashName(),
            'thumbnail' => 'audio-thumbnails/' . $thumbnail->hashName(),
            'name' => 'Audio 1',
            'ft' => '',
            'audio_album_id' => $album->id,
            'genre' => 'Country',
            'released' => '2020-04-01',
            'description' => 'This is video',
        ]);

        // Get the new resource
        $this->get('api/audios/' . $audio->id)
            ->assertJsonFragment([
                'audio' => '/storage/audios/' . $audioFile->hashName(),
                'thumbnail' => '/storage/audio-thumbnails/' . $thumbnail->hashName(),
                'name' => 'Audio 1',
                'ft' => null,
                'audioAlbumId' => $album->id,
                'genre' => 'Country',
                'description' => 'This is video',
            ], $escape = true);

        Storage::assertExists('public/audio-thumbnails/' . $thumbnail->hashName());
        Storage::assertExists('public/audios/' . $audioFile->hashName());

        Storage::delete('public/audio-thumbnails/' . $thumbnail->hashName());
        Storage::delete('public/audios/' . $audioFile->hashName());
    }

    /**
     * Audio Like
     *
     * @return void
     */
    public function test_user_can_like_audio()
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

        // Store
        $response = $this->post('api/audio-likes', [
            'audio' => $audio->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("audio_likes", [
            "audio_id" => $audio->id,
            "username" => $user->username,
        ]);
    }

    /**
     * Audio Comment
     *
     * @return void
     */
    public function test_user_can_comment_on_audio()
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

        // Store
        $response = $this->post('api/audio-comments', [
            'id' => $audio->id,
            "text" => "Some text",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("audio_comments", [
            "audio_id" => $audio->id,
            "text" => "Some text",
            "username" => $user->username,
        ]);
    }

    /**
     * Audio Comment Like
     *
     * @return void
     */
    public function test_user_can_like_audio_comment()
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

        $comment = AudioComment::all()->random();

        // Store
        $response = $this->post('api/audio-comment-likes', [
            "comment" => $comment->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("audio_comment_likes", [
            "audio_comment_id" => $comment->id,
            "username" => $user->username,
        ]);
    }

    /**
     * Test User can buy Audios.
     *
     * @return void
     */
    public function test_user_can_buy_audios()
    {
        // Run the DatabaseSeeder...
        $this->seed();

        Sanctum::actingAs(
            $user = User::all()->random(),
            ['*']
        );

        Notification::fake();

        Mail::fake();

        $musician = User::all()->random();

        Audio::factory()
            ->count(10)
            ->create([
                'audio_album_id' => AudioAlbum::all()->random()->id,
                "username" => $musician->username,
            ]);

        $response = $this->post('api/bought-audios');

        $response->assertStatus(200);

        $this->assertDatabaseCount('bought_audios', 20);

        $this->assertDatabaseCount('decos', 1);

        Notification::assertSentTo($user, AudioReceiptNotification::class);

        // Mail::assertSent(AudioReceiptMail::class);

        // $this->assertDatabaseHas("notifications", [
        // "notifiable_id" => $user->id,
        // "type" => "App\Notifications\AudioReceiptNotification",
        // ]);

        // $this->assertDatabaseHas("notifications", [
        // "notifiable_id" => $user->id,
        // "type" => "App\Notifications\DecoNotification",
        // ]);

        // $this->assertDatabaseCount("notifications", 22);

    }
}
