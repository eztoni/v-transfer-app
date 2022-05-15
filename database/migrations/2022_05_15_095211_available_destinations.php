<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AvailableDestinations extends Migration
{
    public function up()
    {
        Schema::create('destination_user', function (Blueprint $table) {
            $table->foreignId('destination_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('available_destinations');
    }
}
