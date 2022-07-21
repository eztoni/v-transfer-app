<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\Destination::class);
            $table->foreignIdFor(\App\Models\Transfer::class)->nullable();

            $table->text('type');
            $table->integer('max_luggage');
            $table->integer('max_occ');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
