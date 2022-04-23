<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RouteTransfer extends Migration
{
    public function up()
    {
        Schema::create('route_transfer', function (Blueprint $table) {
            $table->foreignId('route_id')->constrained();
            $table->foreignId('transfer_id')->constrained();
            $table->foreignId('partner_id')->default(0);
            $table->bigInteger('price');
            $table->timestamps();

            $table->unique(['route_id', 'transfer_id','partner_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_transfer');
    }
}
