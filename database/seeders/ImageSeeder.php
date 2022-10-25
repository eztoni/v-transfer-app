<?php

namespace Database\Seeders;

use App\Models\Extra;
use App\Models\Transfer;
use App\Models\Vehicle;
use Database\Tools\SeedImagesToModels;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    public function run()
    {
        (new  SeedImagesToModels())(Transfer::all(),'transferImages');
        (new  SeedImagesToModels())(Vehicle::all(),'vehicleImages');
        (new  SeedImagesToModels())(Extra::all(),'extraImages');
    }
}
