<?php

namespace Database\Seeders;

use Database\Tools\SeedImagesToModels;
use Illuminate\Database\Seeder;

class ExtrasSeeder extends Seeder
{
    public function run()
    {
        $extras = \App\Models\Extra::factory(6)->create();
        (new  SeedImagesToModels())($extras,'extraImages');
    }
}
