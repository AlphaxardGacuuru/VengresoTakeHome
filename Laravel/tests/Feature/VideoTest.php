<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoAlbum;
use App\Models\VideoComment;
use App\Notifications\VideoReceiptNotification;
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

class VideoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Index
     *
     * @return void
     */
    public function test_user_can_view_video_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();
        User::factory()->count(10)->create();

        $username = User::all()->random()->username;

        // Create Video Album
        VideoAlbum::factory()->create(['username' => $username]);

        $album = VideoAlbum::first()->id;

        Video::factory()
            ->count(10)
            ->create([
                'username' => $username,
                'video_album_id' => $album,
            ]);

        $response = $this->get('api/videos');

        $response->assertStatus(200);
    }

    /**
     * Show
     *
     * @return void
     */
    public function test_user_can_view_one_video_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();
        User::factory()->count(10)->create();

        $username = User::all()->random()->username;

        // Create Video Album
        VideoAlbum::factory()->create(['username' => $username]);

        $album = VideoAlbum::first()->id;

        Video::factory()
            ->create([
                'username' => $username,
                'video_album_id' => $album,
            ]);

        $video = Video::first()->id;

        $response = $this->get('api/videos/' . $video);

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_create_video()
    {
        Sanctum::actingAs(
            $user = User::factory()->black()->create(),
            ['*']
        );

        $user2 = User::factory()->create();

        $album = VideoAlbum::factory()->create(['username' => $user->username]);

        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');

        $video = UploadedFile::fake()->create('video.mp4');

        // Upload video and thumbnail
        $this->post('api/filepond/video-thumbnail', ['filepond-thumbnail' => $thumbnail]);
        $this->post('api/filepond/video', ['filepond-video' => $video]);

        $response = $this->post('api/videos', [
            'video' => 'videos/' . $video->hashName(),
            'thumbnail' => 'video-thumbnails/' . $thumbnail->hashName(),
            'name' => 'Video 1',
            'ft' => $user2->username,
            'video_album_id' => $album->id,
            'genre' => 'Country',
            'released' => '2020-04-01',
            'description' => 'This is video',
        ]);

        $response->assertStatus(200);

        Storage::assertExists('public/video-thumbnails/' . $thumbnail->hashName());
        Storage::assertExists('public/videos/' . $video->hashName());

        Storage::delete('public/video-thumbnails/' . $thumbnail->hashName());
        Storage::delete('public/videos/' . $video->hashName());
    }

    /**
     * Store Bad
     *
     * @return void
     */
    public function test_user_cannot_create_video_with_bad_data()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $response = $this->post('api/videos', []);

        $response->assertStatus(302);
    }

    /**
     * Update
     *
     * @return void
     */
    public function test_user_can_update_video()
    {
        Sanctum::actingAs(
            $user = User::factory()->black()->create(),
            ['*']
        );

        // Create Video Album
        $album = VideoAlbum::factory()->create(['username' => $user->username]);

        $video = Video::factory()
            ->create([
                'username' => $user->username,
                'video_album_id' => $album,
            ]);

        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');

        $videoFile = UploadedFile::fake()->create('video.mp4');

        // Upload video and thumbnail
        $uploadThumbnail = $this->post('api/filepond/video-thumbnail', ['filepond-thumbnail' => $thumbnail]);
        $uploadVideo = $this->post('api/filepond/video', ['filepond-video' => $videoFile]);

        $uploadThumbnail->assertStatus(200);
        $uploadVideo->assertStatus(200);

        $this->put('api/videos/' . $video->id, [
            'video' => 'videos/' . $videoFile->hashName(),
            'thumbnail' => 'video-thumbnails/' . $thumbnail->hashName(),
            'name' => 'Video 1',
            'ft' => '',
            'video_album_id' => $album->id,
            'genre' => 'Country',
            'released' => '2020-04-01',
            'description' => 'This is video',
        ]);

        // Get the new resource
        $this->get('api/videos/' . $video->id)
            ->assertJsonFragment([
                'video' => '/storage/videos/' . $videoFile->hashName(),
                'thumbnail' => '/storage/video-thumbnails/' . $thumbnail->hashName(),
                'name' => 'Video 1',
                'ft' => null,
                'videoAlbumId' => $album->id,
                'genre' => 'Country',
                'description' => 'This is video',
            ], $escape = true);

        Storage::assertExists('public/video-thumbnails/' . $thumbnail->hashName());
        Storage::assertExists('public/videos/' . $videoFile->hashName());

        Storage::delete('public/video-thumbnails/' . $thumbnail->hashName());
        Storage::delete('public/videos/' . $videoFile->hashName());
    }

    /**
     * Video Like
     *
     * @return void
     */
    public function test_user_can_like_video()
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

        $video = Video::all()->random();

        // Store
        $response = $this->post('api/video-likes', [
            'video' => $video->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("video_likes", [
            "video_id" => $video->id,
            "username" => $user->username,
        ]);
    }

    /**
     * Video Comment
     *
     * @return void
     */
    public function test_user_can_comment_on_video()
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

        $video = Video::all()->random();

        // Store
        $response = $this->post('api/video-comments', [
            'id' => $video->id,
            "text" => "Some text",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("video_comments", [
            "video_id" => $video->id,
            "text" => "Some text",
            "username" => $user->username,
        ]);
    }

    /**
     * Video Comment Like
     *
     * @return void
     */
    public function test_user_can_like_video_comment()
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

        $comment = VideoComment::all()->random();

        // Store
        $response = $this->post('api/video-comment-likes', [
            "comment" => $comment->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("video_comment_likes", [
            "video_comment_id" => $comment->id,
            "username" => $user->username,
        ]);
    }

    /**
     * Test User can buy Videos.
     *
     * @return void
     */
    public function test_user_can_buy_videos()
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

        Video::factory()
            ->count(10)
            ->create([
                'video_album_id' => VideoAlbum::all()->random()->id,
                "username" => $musician->username,
            ]);

        $response = $this->post('api/bought-videos');

        $response->assertStatus(200);

        $this->assertDatabaseCount('bought_videos', 20);

        $this->assertDatabaseCount('decos', 1);

        Notification::assertSentTo($user, VideoReceiptNotification::class);
    }
}
