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
            $table->foreignIdFor(\App\Models\Partner::class);
            $table->foreignIdFor(\App\Models\Transfer::class);

            $table->foreignIdFor(\App\Models\Reservation::class,'round_trip_id')->nullable();
            $table->boolean('is_main')->default(true);

            $table->integer('price');

            $table->string('pickup_address');
            $table->string('dropoff_address');

            $table->string('flight_number')->nullable();
            $table->text('remark')->nullable();

            $table->dateTime('dateTime');


            $table->integer('adults');
            $table->integer('children');
            $table->integer('infants');

            $table->integer('luggage');

            $table->json('child_seats')->nullable();
            $table->json('price_breakdown');

            $table->enum('status',\App\Models\Reservation::STATUS_ARRAY)->default(\App\Models\Reservation::STATUS_CONFIRMED);


            $table->enum('confirmation_language',array_keys(\App\Models\Reservation::CONFIRMATION_LANGUAGES))->default('en');

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
