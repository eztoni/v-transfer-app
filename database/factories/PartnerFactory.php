<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        return [
            'destination_id' => function (){
              return   Destination::inRandomOrder()->first()->id;
            },
            'name' => $this->faker->company(),
            'contact' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
