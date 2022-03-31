<?php

namespace Database\Factories;

use App\Models\Extra;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ExtraFactory extends Factory
{
    protected $model = Extra::class;


    public function definition(): array
    {
        return [
            'owner_id' => 1,
            'name' => $this->faker->word(),
            'description' => $this->faker->text(),
            'price' => random_int(100,10000),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
