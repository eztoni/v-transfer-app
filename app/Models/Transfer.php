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

class Transfer extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasTranslations;
    use HasFactory;

    protected $with = ['vehicle'];

    // First index must be 1 because wire ui has a bug
    public const CHILD_SEATS = [
      0=>'Booster ( 10-15 kg )',
      1=>'Egg ( 0-5 kg )',
      2=>'Classic ( 5-10 kg )',
    ];

    const MAX_IMAGES = 5;
    const IMAGE_PRIMARY_PROPERTY = 'primary';

    protected $fillable = [
        'name',
    ];
    public $translatable = ['name'];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('transferImages')
            ->useFallbackUrl('	https://app.ez-booker.com/modules/img/default_image.jpg');
    }
    public function getPrimaryImageUrlAttribute()
    {
        $image = $this->getMedia('transferImages', function (Media $media) {
            return isset($media->custom_properties[self::IMAGE_PRIMARY_PROPERTY]);
        })->first();

        if (!$image) {
            return $this->getFirstMediaUrl('transferImages');
        }

        return $image->getFullUrl();

    }
    public function getPrimaryImageAttribute(){
        $image = $this->getMedia('transferImages', function (Media $media) {
            return isset($media->custom_properties[self::IMAGE_PRIMARY_PROPERTY]);
        })->first();

        if(!$image){
            $image = $this->getMedia('transferImages')->first();
        }
        return $image;
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
        static::addGlobalScope(new DestinationScope());
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class)->withPivot(['price','round_trip','price_round_trip','commission','discount','tax_level','calculation_type','date_from','date_to']);
    }

}
