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
            $table->foreignIdFor(\App\Models\Destination::class,);

            $table->foreignIdFor(\App\Models\Point::class,'pickup_location');
            $table->foreignIdFor(\App\Models\Point::class,'dropoff_location');

            $table->string('pickup_address');
            $table->string('flight_number')->nullable();
            $table->text('remark')->nullable();
            $table->enum('confirmation_language',array_keys(\App\Models\Reservation::CONFIRMATION_LANGUAGES))->default('en');

            $table->foreignIdFor(\App\Models\Reservation::class,'round_trip_id')->nullable();



            $table->string('dropoff_address');

            $table->integer('adults');
            $table->integer('children');
            $table->integer('infants');

            $table->integer('luggage');

            $table->boolean('round_trip');

            $table->foreignIdFor(\App\Models\Partner::class);

            $table->json('route');

            $table->json('extras')->nullable();

            $table->json('child_seats')->nullable();

            $table->json('transfer');

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
