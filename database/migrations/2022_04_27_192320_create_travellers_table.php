<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravellersTable extends Migration
{
    public function up()
    {
        Schema::create('travellers', function (Blueprint $table) {
            $table->id();

            $table->string('title')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('reservation_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('travellers');
    }
}
