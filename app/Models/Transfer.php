<?php

namespace App\Models;

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
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope());
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
        return $this->belongsToMany(Route::class)->withPivot(['price']);
    }
}
