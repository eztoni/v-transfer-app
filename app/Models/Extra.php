<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use App\Scopes\OwnerScope;
use Cknow\Money\Casts\MoneyDecimalCast;
use Cknow\Money\Casts\MoneyIntegerCast;
use Cknow\Money\Casts\MoneyStringCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use function Clue\StreamFilter\fun;

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
    public function getPrimaryImageUrlAttribute()
    {
        $image = $this->getMedia('extraImages', function (Media $media) {
            return isset($media->custom_properties[self::IMAGE_PRIMARY_PROPERTY]);
        })->first();

        if (!$image) {
            return $this->getFirstMediaUrl('extraImages');
        }

        return $image->getFullUrl();

    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('extraImages')
            ->useFallbackUrl('	https://app.ez-booker.com/modules/img/default_image.jpg');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);
    }

    public static function getExtrasByPartnerIdWithPrice(int $partnerId,array $with = []):Collection
    {
        return self::whereHas('partner',function($q)use($partnerId){
            $q->where('id',$partnerId);
        })->with(array_merge(['partner'=>function($q) use($partnerId){
            $q->where('id',$partnerId);
        }],$with))->get();

    }

    public function partner()
    {
        return $this->belongsToMany(Partner::class)->withPivot(['price','commission','discount','tax_level','calculation_type','date_from','date_to']);
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
