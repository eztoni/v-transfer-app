<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerDestinationSeeder extends Seeder
{
    public function run()
    {
        $destination = Destination::first();

        Partner::each(function ($i)use($destination){
           $destination->partners()->attach($i);
        });
    }
}
