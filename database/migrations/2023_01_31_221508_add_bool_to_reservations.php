<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBoolToReservations extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->boolean('included_in_accommodation_reservation')->after('is_main')->default(0);
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            //
        });
    }
}
