<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickupDropoffIdsToReservations extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {

            $table->foreignIdFor(\App\Models\Point::class,'pickup_address_id')
                ->after('pickup_address')
                ->nullable();

            $table->foreignIdFor(\App\Models\Point::class,'dropoff_address_id')
                ->after('dropoff_address')
                ->nullable();
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('pickup_address_id');
            $table->dropColumn('dropoff_address_id');
        });
    }
}
