<?php
namespace Database\Tools;

use Faker\Factory;

class SeedImagesToModels
{

    public function __invoke(\Illuminate\Support\Collection $models,$collectionName,$rand_min=0,$rand_max=5)
    {
        $faker = Factory::create();

        foreach($models as $model){
            for($i = 0; $i < random_int($rand_min,$rand_max); $i++){
                $imageUrl = $faker->imageUrl();
                $model->addMediaFromUrl($imageUrl)->toMediaCollection($collectionName);
            }
        }

    }

}
