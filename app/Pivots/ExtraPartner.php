<?php

namespace App\Pivots;

use App\Models\Extra;
use App\Models\Partner;

class ExtraPartner extends \Illuminate\Database\Eloquent\Relations\Pivot
{
    public function extra() {
        return $this->belongsTo(Extra::class);
    }

    public function partner() {
        return $this->belongsTo(Partner::class);
    }
}
