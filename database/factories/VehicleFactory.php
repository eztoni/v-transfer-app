<?php

namespace Database\Factories;

use App\Models\Owner;
use App\Models\Transfer;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'owner_id' => 1,
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'max_luggage' => $this->faker->randomNumber(),
            'max_occ' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'transfer_id' => Transfer::factory(),
        ];
    }
}
