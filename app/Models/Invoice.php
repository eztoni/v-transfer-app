<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'zki',
        'jir',
        'invoice_id',
        'invoice_establishment',
        'invoice_device',
    ];

}
