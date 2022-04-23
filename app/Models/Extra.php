<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Scopes\OwnerScope;
use Cknow\Money\Casts\MoneyDecimalCast;
use Cknow\Money\Casts\MoneyIntegerCast;
use Cknow\Money\Casts\MoneyStringCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Extra extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasTranslations;
    const MAX_IMAGES = 5;

    #DONT CHANGE
    const IMAGE_PRIMARY_PROPERTY = 'primary';

    protected $fillable = [
        'name',
        'description',
    ];

    public $translatable = ['name','description'];

    protected $casts = [
      'price' => MoneyIntegerCast::class. ':EUR,true',
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


    public function getPrice($partnerId){

        $price = 0;
        $extraPrice = (\DB::table('extra_partner')
            ->select('price')
            ->where('partner_id','=',$partnerId)
            ->where('extra_id','=',$this->id)->first());

        if($extraPrice){
            $price = $extraPrice->price;
        }

        return $price;
    }

    protected static function booted()
    {
        static::addGlobalScope(new OwnerScope());
    }
}
