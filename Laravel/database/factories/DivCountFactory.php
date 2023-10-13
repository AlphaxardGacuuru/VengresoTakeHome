<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DivCount>
 */
class DivCountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "url" => fake()->url(),
            "count" => rand(100, 500),
            "created_at" => Carbon::now()->subDays(rand(10, 100)),
        ];
    }
}
