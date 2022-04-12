<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtraPartner extends Migration
{
    public function up()
    {
        Schema::create('extra_partner', function (Blueprint $table) {
            $table->foreignId('extra_id')->constrained();
            $table->foreignId('partner_id')->default(0);
            $table->bigInteger('price');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('extra_partner');
    }
}
