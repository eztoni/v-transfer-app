<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtraReservation extends Migration
{
    public function up()
    {
        Schema::create('extra_reservation', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Extra::class);
            $table->foreignIdFor(\App\Models\Reservation::class);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('');
    }
}
