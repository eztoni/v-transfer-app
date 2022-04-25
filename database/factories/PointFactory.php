<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Destination;
use App\Models\Owner;
use App\Models\Point;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PointFactory extends Factory
{
    protected $model = Point::class;

    public function definition(): array
    {
        return [

            'owner_id' => 1,
            'destination_id' => 1,
            'name' => $this->faker->city(),
            'description' => $this->faker->text(),
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'type' => function(){
                return \Arr::random(Point::TYPE_ARRAY);
            },
            'pms_code' => $this->faker->word(),
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
