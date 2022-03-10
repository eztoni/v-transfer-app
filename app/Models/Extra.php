<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Cknow\Money\Casts\MoneyDecimalCast;
use Cknow\Money\Casts\MoneyIntegerCast;
use Cknow\Money\Casts\MoneyStringCast;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Extra extends Model implements HasMedia
{

    use InteractsWithMedia;
    const MAX_IMAGES = 5;

    #DONT CHANGE
    const IMAGE_PRIMARY_PROPERTY = 'primary';

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    protected $casts = [
      'price' => MoneyIntegerCast::class,
    ];

    public function getPrimaryImageAttribute(){
        $image = $this->getMedia('extraImages', function (Media $media) {
            return isset($media->custom_properties[self::IMAGE_PRIMARY_PROPERTY]);
        })->first();

        if(!$image){
            $image = $this->getMedia('extraImages')->first();
        }
        return $image;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('extraImages');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }
}
