<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ChatSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Index.
     *
     * @return void
     */
    public function test_chat_threads_are_accessible()
    {
        $this->seed([
            UserSeeder::class,
            ChatSeeder::class,
        ]);

        $response = $this->get('api/chats');

        $response->assertStatus(200);
    }

    /**
     * Store
     *
     * @return void
     */
    public function test_user_can_send_chats()
    {
        Sanctum::actingAs(
            $user = User::factory()->black()->create(),
            ['*']
        );

        $user2 = User::all()->random();

        $text = fake()->realText($maxNbChars = 20, $indexSize = 2);

        $image = UploadedFile::fake()->image('avatar.jpg');

        // Upload media
        $uploadImage = $this->post('api/filepond/chats', ['filepond-media' => $image]);

        $uploadImage->assertStatus(200);

        $response = $this->post('api/chats', [
            "to" => $user2->username,
            "text" => $text,
            "media" => $image,
        ]);

        $response->assertStatus(200);

        Storage::assertExists('public/chat-media/' . $image->hashName());

        Storage::delete('public/chat-media/' . $image->hashName());
    }

    /**
     * Show
     *
     * @return void
     */
    public function test_user_can_view_one_chat()
    {
        Sanctum::actingAs(
            $user = User::factory()->black()->create(),
            ['*']
        );

        $user2 = User::all()->random();

        $text = fake()->realText($maxNbChars = 20, $indexSize = 2);

        $this->post('api/chats', [
            "to" => $user2->username,
            "text" => $text,
        ]);

        $response = $this->get('api/chats/' . $user->username);

        $response->assertStatus(200);

        $this->assertDatabaseHas("chats", ["id" => $response["data"][0]["id"]]);
    }

    /**
     * Destroy
     *
     * @return void
     */
    public function test_user_can_delete_chat()
    {
        Sanctum::actingAs(
            $user = User::factory()->black()->create(),
            ['*']
        );

        $user2 = User::all()->random();

        $text = fake()->realText($maxNbChars = 20, $indexSize = 2);

        $this->post('api/chats', [
            "to" => $user2->username,
            "text" => $text,
        ]);

        $chat = $this->get("api/chats/" . $user->username);

        $response = $this->delete('api/chats/' . $chat["data"][0]["id"]);

        $this->assertDatabaseMissing("chats", [
            "id" => $chat["data"][0]["id"],
        ]);

        $response->assertStatus(200);
    }
}
