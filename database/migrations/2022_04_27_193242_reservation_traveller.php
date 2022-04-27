<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReservationTraveller extends Migration
{
    public function up()
    {
        Schema::create('', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Traveller::class);
            $table->foreignIdFor(\App\Models\Reservation::class);

            $table->boolean('lead')->default(true);
            $table->string('comment','300')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('');
    }
}
