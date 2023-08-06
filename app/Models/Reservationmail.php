<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservationmail extends Model
{
    use HasFactory;

    protected $table = 'reservationmails';

    protected $fillable = [
        'reservation_id',
        'from',
        'to',
        'email_type'
    ];
}
