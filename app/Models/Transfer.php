<?php

namespace App\Models;

use App\Scopes\OwnerScope;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Transfer extends Model implements HasMedia
{
    use InteractsWithMedia;
    const MAX_IMAGES = 5;
    const IMAGE_PRIMARY_PROPERTY = 'primary';

    protected $fillable = [
        'name',

    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('transferImages');
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

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }
}
