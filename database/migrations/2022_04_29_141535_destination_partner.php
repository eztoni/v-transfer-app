<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DestinationPartner extends Migration
{
    public function up()
    {
        Schema::create('destination_partner', function (Blueprint $table) {
            $table->foreignId('destination_id')->constrained();
            $table->foreignId('partner_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('destination_partner');
    }
}
