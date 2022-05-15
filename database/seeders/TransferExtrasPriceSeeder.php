<?php

namespace Database\Seeders;

use App\Models\Extra;
use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;
use Illuminate\Database\Seeder;

class TransferExtrasPriceSeeder extends Seeder
{
    public function run()
    {


        foreach (Partner::whereOwnerId(1)->get() as $partner) {
            foreach (Extra::whereOwnerId(1)->get() as $extra)
                \DB::table('extra_partner')->insert(
                    [
                        'extra_id' => $extra->id,
                        'partner_id' => $partner->id,
                        'price' => rand(1000, 100000),
                    ]
                );
            foreach (Transfer::whereOwnerId(1)->get() as $transfer) {
                foreach (Route::whereOwnerId(1)->get() as $route)
                    \DB::table('route_transfer')->insert(
                        [
                            'route_id' => $route->id,
                            'transfer_id' => $transfer->id,
                            'partner_id' => $partner->id,
                            'price' => rand(1000, 100000),
                            'price_round_trip' => rand(1000, 100000),

                        ]
                    );

            }


        }


    }
}
