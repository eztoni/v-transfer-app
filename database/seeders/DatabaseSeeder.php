<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        #It will create the companies aswell
        \App\Models\Destination::factory(2)->create();
        \App\Models\Point::factory(40)->create();
        \App\Models\Partner::factory(4)->create();
        \App\Models\Route::factory(6)->create();
        \App\Models\Vehicle::factory(6)->create();
    }
}
