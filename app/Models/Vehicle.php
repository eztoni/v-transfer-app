<?php

namespace App\Models;

use App\Scopes\DestinationScope;
use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Vehicle extends Model implements HasMedia
{
    use HasTranslations;
    use HasFactory;
    use InteractsWithMedia;
    const MAX_IMAGES = 5;

    #DONT CHANGE
    const IMAGE_PRIMARY_PROPERTY = 'primary';

    protected $fillable = [
        'type',
        'max_luggage',
        'max_occ',
    ];
    public $translatable = ['type'];

    public function getPrimaryImageAttribute(){
        $image = $this->getMedia('vehicleImages', function (Media $media) {
            return isset($media->custom_properties[self::IMAGE_PRIMARY_PROPERTY]);
        })->first();

        if(!$image){
            $image = $this->getMedia('vehicleImages')->first();
        }
        return $image;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('vehicleImages');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new DestinationScope());
    }

}
