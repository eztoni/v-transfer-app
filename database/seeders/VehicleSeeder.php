<?php

namespace Database\Seeders;

use Database\Tools\SeedImagesToModels;
use Faker\Factory;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        $vehicles =    \App\Models\Vehicle::factory(5)->create();
        (new  SeedImagesToModels())($vehicles,'vehicleImages');
    }
}
