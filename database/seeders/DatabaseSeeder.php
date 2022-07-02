<?php

namespace Database\Seeders;

use App\Models\Transfer;
use Database\Tools\SeedImagesToModels;
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
        \App\Models\Point::factory(40)->create();
        \App\Models\Partner::factory(4)->create();
        \App\Models\Route::factory(6)->create();




        $this->call([
            VehicleSeeder::class,
            ExtrasSeeder::class,
            TransferExtrasPriceSeeder::class,
            PartnerDestinationSeeder::class,
        ]);
        (new  SeedImagesToModels())(Transfer::all(),'transferImages');

    }
}
