<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeCategory extends Model
{
    use HasFactory;

    const ADULT = 'adult';
    const CHILD = 'child';
    const INFANT = 'infant';

    const AGE_CATEGORIES = array(
    self::ADULT,self::CHILD,self::INFANT
    );

    public function getCategoryAttribute($value){
        return \Str::ucfirst($value);
    }

    protected $fillable = [
        'age_group_id',
        'category',
        'age_from',
        'age_to',
    ];

}
