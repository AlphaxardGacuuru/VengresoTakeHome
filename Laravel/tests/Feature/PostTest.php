<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Database\Seeders\PostSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Index
     *
     * @return void
     */
    public function test_user_can_view_post_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();
        User::factory()->count(10)->create();

        Post::factory()
            ->count(10)
            ->create(['username' => User::all()->random()->username]);

        $response = $this->get('api/posts');

        $response->assertStatus(200);
    }

    /**
     * Show
     *
     * @return void
     */
    public function test_user_can_view_one_post_resource()
    {
        // Create Users with @blackmusic first
        User::factory()->black()->create();

        $post = Post::factory()
            ->create(['username' => User::all()->random()->username]);

        $response = $this->get('api/posts/' . $post->id);

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_create_post()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $image = UploadedFile::fake()->image('avatar.jpg');

        // Upload media
        $uploadImage = $this->post('api/filepond/posts', ['filepond-media' => $image]);

        $uploadImage->assertStatus(200);

        $response = $this->post('api/posts', [
            'text' => 'Some text',
            'media' => $image,
        ]);

        $response->assertStatus(200);

        Storage::assertExists('public/post-media/' . $image->hashName());

        Storage::delete('public/post-media/' . $image->hashName());
    }

    /**
     * Update
     *
     * @return void
     */
    public function test_user_can_update_post()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $response = $this->post('api/posts', [
            'text' => 'Some text',
        ]);

        $response = $this->post('api/posts', [
            'text' => 'Some text 2',
            "__method" => "PUT",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("posts", [
            "text" => "Some text 2",
        ]);
    }

    /**
     * Destroy
     *
     * @return void
     */
    public function test_user_can_delete_post()
    {
        Sanctum::actingAs(
            User::factory()->black()->create(),
            ['*']
        );

        $image = UploadedFile::fake()->image('avatar.jpg');

        // Upload media
        $uploadImage = $this->post('api/filepond/posts', ['filepond-media' => $image]);

        $uploadImage->assertStatus(200);

        // Store
        $response = $this->post('api/posts', [
            'text' => 'Some text',
            'media' => $image,
        ]);

        $response->assertStatus(200);

        Storage::assertExists('public/post-media/' . $image->hashName());

        Storage::delete('public/post-media/' . $image->hashName());
    }

    /**
     * Post Like
     *
     * @return void
     */
    public function test_user_can_like_post()
    {
        $this->seed([
            UserSeeder::class,
            PostSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $post = Post::all()->random();

        $posterId = User::where("username", $post->username)
            ->get()
            ->first()
            ->id;

        // Store
        $response = $this->post('api/post-likes', [
            'post' => $post->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("post_likes", [
            "post_id" => $post->id,
            "username" => $user->username,
        ]);

        $this->assertDatabaseHas("notifications", [
            "notifiable_id" => $posterId,
            "type" => "App\Notifications\PostLikedNotification",
        ]);
    }

    /**
     * Post Comment
     *
     * @return void
     */
    public function test_user_can_comment_on_post()
    {
        $this->seed([
            UserSeeder::class,
            PostSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $post = Post::all()->random();

        $posterId = User::where("username", $post->username)
            ->get()
            ->first()
            ->id;

        // Store
        $response = $this->post('api/post-comments', [
            'id' => $post->id,
            "text" => "Some text",
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("post_comments", [
            "post_id" => $post->id,
            "text" => "Some text",
            "username" => $user->username,
        ]);

        $this->assertDatabaseHas("notifications", [
            "notifiable_id" => $posterId,
            "type" => "App\Notifications\PostCommentedNotification",
        ]);
    }

    /**
     * Post Comment Like
     *
     * @return void
     */
    public function test_user_can_like_post_comment()
    {
        $this->seed([
            UserSeeder::class,
            PostSeeder::class,
        ]);

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $comment = PostComment::all()->random();
        
		$commenterId = User::where("username", $comment->username)
            ->get()
            ->first()
			->id;

        // Store
        $response = $this->post('api/post-comment-likes', [
            "comment" => $comment->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("post_comment_likes", [
            "post_comment_id" => $comment->id,
            "username" => $user->username,
        ]);

        $this->assertDatabaseHas("notifications", [
            "notifiable_id" => $commenterId,
            "type" => "App\Notifications\PostCommentLikedNotification",
        ]);
    }
}
