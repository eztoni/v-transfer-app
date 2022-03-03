<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Point;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        return [
            'destination_id' => function (){
                return Destination::inRandomOrder()->first()->id;
            },
            'name' => $this->faker->name(),
            'starting_point_id' => function ($attr){
                return Point::whereDestinationId($attr['destination_id'])->inRandomOrder()->first()->id;
            },
            'ending_point_id' => function ($attr){
                return Point::whereDestinationId($attr['destination_id'])->where('id','!=',$attr['starting_point_id'])->inRandomOrder()->first()->id;
            },
            'his_code' => $this->faker->word(),
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),


        ];
    }
}
