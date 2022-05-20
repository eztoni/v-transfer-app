<?php

namespace App\Pivots;

use App\Models\Extra;
use App\Models\Partner;
use App\Models\Route;
use App\Models\Transfer;

class RouteTransfer extends \Illuminate\Database\Eloquent\Relations\Pivot
{
    public function partner() {
        return $this->belongsTo(Partner::class);
    }

    public function transfer() {
        return $this->belongsTo(Transfer::class);
    }

    public function route() {
        return $this->belongsTo(Route::class);
    }
}

