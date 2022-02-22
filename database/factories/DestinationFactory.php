<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'company_id' => function () {
                return Company::inRandomOrder()->first()->id;
            },
        ];
    }
}
