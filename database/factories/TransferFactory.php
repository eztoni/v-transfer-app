<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        return [
            'owner_id' => 1,
            'name' => $this->faker->sentence(2),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'destination_id' => 1,
        ];
    }
}
