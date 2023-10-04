<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
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
        // Check if @blackmusic exists
        $blackDoesntExist = User::where('username', '@blackmusic')
            ->doesntExist();

        // Check if @alphaxardG exists
        $alDoesntExist = User::where('username', '@alphaxardG')
            ->doesntExist();

        if ($blackDoesntExist) {
            User::factory()
                ->black()
                ->hasKopokopos(1)
                ->create();
        }

        if ($alDoesntExist) {
            User::factory()
                ->al()
                ->hasKopokopos(1)
                ->create();
        }

        User::factory()
            ->count(10)
            ->unverified()
            ->hasKopokopos(1)
            ->create();
    }
}
