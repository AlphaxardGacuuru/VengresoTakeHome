<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
		$phone = fake()->phoneNumber();
        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'email_verified_at' => now(),
            'password' => Hash::make($phone),
            // 'remember_token' => Str::random(10),
            'phone' => $phone,
            'avatar' => 'avatars/male-avatar.png',
        ];
    }

    /**
     * Add John's Account
     *
     * @return static
     */
    public function john()
    {
        return $this->state(fn(array $attributes) => [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'email_verified_at' => now(),
            'avatar' => 'avatars/male-avatar.png',
            'phone' => '0700000000',
            'password' => Hash::make('0700000000'),
            // 'remember_token' => Str::random(10),
        ]);
    }
}
