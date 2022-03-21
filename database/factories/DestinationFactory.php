<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Destination;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'owner_id' => function () {
                return Owner::inRandomOrder()->first()->id;
            },
        ];
    }
}
