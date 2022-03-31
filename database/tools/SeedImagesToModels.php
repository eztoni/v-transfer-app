<?php
namespace Database\Tools;

use Faker\Factory;

class SeedImagesToModels
{

    public function __invoke(\Illuminate\Support\Collection $models,$collectionName,$rand_min=0,$rand_max=5)
    {
        $faker = Factory::create();
        $imageUrl = 'https://api.lorem.space/image/car?hash=82560';
        foreach($models as $model){
            for($i = 0; $i < random_int($rand_min,$rand_max); $i++){

                $model->addMediaFromUrl($imageUrl)->toMediaCollection($collectionName);
            }
        }

    }

}
