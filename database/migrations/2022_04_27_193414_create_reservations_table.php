<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Point::class,'pickup_location');
            $table->foreignIdFor(\App\Models\Point::class,'dropoff_location');

            $table->string('pickup_address');
            $table->string('dropoff_address');

            $table->integer('adults');
            $table->integer('children');
            $table->integer('infants');

            $table->integer('luggage');

            $table->boolean('two_way');

            $table->foreignIdFor(\App\Models\Partner::class);

            $table->text('route');

            $table->text('transfer');

            $table->integer('price');

            $table->date('date');
            $table->time('time');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
