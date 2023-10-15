<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $johnDoesntExist = User::where("email", "johndoe@gmail.com")
            ->doesntExist();

        $johnDoesntExist && User::factory()->john()->create();
    }
}
